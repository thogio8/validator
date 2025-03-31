<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\CompiledRulesInterface;
use ValidatorPro\Contracts\ContextInterface;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\ValidationResultInterface;
use ValidatorPro\Contracts\ValidatorInterface;
use ValidatorPro\Rules\CallableRule;
use ValidatorPro\Rules\Common\NullableRule;

class Validator implements ValidatorInterface
{
    /**
     * @var array<string, RuleInterface>
     */
    private array $rules = [];

    /**
     * @var ContextInterface|null
     */
    private ?ContextInterface $context = null;

    public function __construct()
    {
        $this->registerDefaultRules();
    }

    /**
     * Register default validation rules
     */
    private function registerDefaultRules(): void
    {
        $this->addRule('nullable', new NullableRule());
    }

    public function validate(array $data, array|string $rules, array $messages = []): ValidationResultInterface
    {
        $result = new ValidationResult();

        // Appliquer le contexte s'il est défini
        if ($this->context !== null) {
            // On utilise array_merge pour combiner les règles
            if (is_array($rules)) {
                $contextRules = $this->context->getRules();
                // Assurons-nous que les règles ne sont pas vides
                if (! empty($contextRules)) {
                    $rules = array_merge($contextRules, $rules);
                }
            }

            // Combiner les messages d'erreur personnalisés
            $contextMessages = $this->context->getMessages();
            if (! empty($contextMessages)) {
                $messages = array_merge($contextMessages, $messages);
            }

            // Ajouter des attributs de contexte aux données
            $contextAttributes = $this->context->getAttributes();
            foreach ($contextAttributes as $key => $value) {
                if (! isset($data[$key])) {
                    $data[$key] = $value;
                }
            }
        }

        $compiledRules = $this->compile($rules);

        foreach ($compiledRules->getCompiledRules() as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            // Vérifier si le champ est nullable
            $isNullable = false;
            foreach ($fieldRules as $ruleName => $parameters) {
                if ($ruleName === 'nullable') {
                    $isNullable = true;

                    // Il est sûr d'utiliser break ici car nous n'avons plus besoin de vérifier
                    // d'autres règles pour savoir si le champ est nullable
                    break;
                }
            }

            // Si le champ est nullable et que la valeur est null, le marquer comme valide et passer au suivant
            if ($isNullable && $value === null) {
                // Important d'ajouter le champ aux champs validés et aux données valides
                $result->addValidatedField($field);
                $result->addValidData($field, $value);

                // Continue est essentiel ici pour passer au champ suivant
                continue;
            }

            // Appliquer toutes les règles au champ
            $fieldValid = true;
            foreach ($fieldRules as $ruleName => $parameters) {
                // Ignorer la règle nullable lors de l'application
                if ($ruleName === 'nullable') {
                    continue;
                }

                $rule = $this->getRule($ruleName);
                // Ignorer les règles qui n'existent pas
                if ($rule === null) {
                    continue;
                }

                // Appliquer la règle
                if (! $rule->validate($value, $parameters, $data)) {
                    // Prioriser les messages selon un ordre précis
                    $messageKey = "{$field}.{$ruleName}";
                    // Vérifier chaque niveau de manière explicite
                    $message = null;
                    if (isset($messages[$messageKey])) {
                        $message = $messages[$messageKey];
                    } elseif (isset($messages[$field])) {
                        $message = $messages[$field];
                    } elseif (isset($messages[$ruleName])) {
                        $message = $messages[$ruleName];
                    } else {
                        $message = $rule->getMessage();
                    }

                    $message = str_replace(':attribute', $field, $message);
                    $result->addError($field, $message);
                    $fieldValid = false;

                    // Break essentiel ici pour arrêter la validation de ce champ
                    break;
                }
            }

            // Toujours marquer le champ comme validé (succès ou échec)
            $result->addValidatedField($field);
            // N'ajouter aux données valides que si la validation a réussi
            if ($fieldValid) {
                $result->addValidData($field, $value);
            }
        }

        return $result;
    }

    public function addRule(string $name, $rule): self
    {
        // Vérifier si c'est un callable mais pas un RuleInterface
        // Le instanceof est plus spécifique et doit être vérifié en premier
        if (is_callable($rule) && ! ($rule instanceof RuleInterface)) {
            // On transforme le callable en CallableRule
            $rule = new CallableRule($rule, $name);
        }

        $this->rules[$name] = $rule;

        return $this;
    }

    public function setContext(ContextInterface $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function compile(array|string $rules): CompiledRulesInterface
    {
        $compiled = new CompiledRules();

        if (is_string($rules)) {
            // Traiter la chaîne comme un ensemble de règles pour un champ unique
            $rulesArray = $this->parseRuleString($rules);
            // Définir un nom de champ par défaut
            $compiled->addField('_default', $rulesArray);

            return $compiled;
        }

        foreach ($rules as $field => $fieldRules) {
            if (is_string($fieldRules)) {
                // Convertir la chaîne en tableau de règles
                $rulesArray = $this->parseRuleString($fieldRules);
                $compiled->addField($field, $rulesArray);
            } elseif (is_array($fieldRules)) {
                // S'assurer que le champ est toujours ajouté
                $compiled->addField($field, $fieldRules);
            }
        }

        return $compiled;
    }

    /**
     * Parse a rule string into a structured array
     *
     * @param string $rulesString
     * @return array<string, array<mixed>>
     */
    private function parseRuleString(string $rulesString): array
    {
        $result = [];
        $rules = explode('|', $rulesString);

        foreach ($rules as $rule) {
            // Limiter à 2 parties - nom de la règle et paramètres
            $parts = explode(':', $rule, 2);
            $ruleName = $parts[0];
            $parameters = [];

            // S'il y a des paramètres, les traiter
            if (isset($parts[1])) {
                $paramsString = $parts[1];
                $inQuotes = false;
                $currentParam = '';

                // Analyser les paramètres caractère par caractère
                $length = strlen($paramsString);
                for ($i = 0; $i < $length; $i++) {
                    $char = $paramsString[$i];

                    // Gestion des guillemets (les comparer explicitement)
                    if ($char === '"') {
                        // Si c'est le premier caractère ou le caractère précédent n'est pas un backslash
                        if ($i === 0 || $paramsString[$i - 1] !== '\\') {
                            $inQuotes = ! $inQuotes;
                        } else {
                            // C'est un guillemet échappé, l'ajouter sans le backslash
                            $currentParam = substr($currentParam, 0, -1) . $char;
                        }
                    }
                    // Si on trouve une virgule hors des guillemets, c'est un séparateur de paramètres
                    elseif ($char === ',' && ! $inQuotes) {
                        $parameters[] = $currentParam;
                        $currentParam = '';
                    }
                    // Sinon, ajouter le caractère au paramètre courant
                    else {
                        $currentParam .= $char;
                    }
                }

                // Ne pas oublier le dernier paramètre
                if ($currentParam !== '') {
                    $parameters[] = $currentParam;
                }
            }

            $result[$ruleName] = $parameters;
        }

        return $result;
    }

    public function extend(string $name, callable|string $extension): self
    {
        // Si c'est un nom de classe, l'instancier
        if (is_string($extension) && class_exists($extension)) {
            $extension = new $extension();
        }

        // Ajouter la règle selon son type
        if ($extension instanceof RuleInterface) {
            $this->addRule($name, $extension);
        } elseif (is_callable($extension)) {
            $this->addRule($name, new CallableRule($extension, $name));
        }

        return $this;
    }

    public function getRule(string $name): ?RuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    public function hasRule(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    public function getAvailableRules(): array
    {
        return $this->rules;
    }
}
