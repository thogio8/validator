<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\CompiledRulesInterface;
use ValidatorPro\Contracts\ContextInterface;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;
use ValidatorPro\Contracts\ValidationResultInterface;
use ValidatorPro\Contracts\ValidatorInterface;

/**
 * Main validator class for data validation.
 *
 * This class is the central component of the ValidatorPro library,
 * responsible for validating data against defined rules and providing
 * structured results. It's designed to be flexible, extensible, and
 * compliant with SOLID principles.
 */
class Validator implements ValidatorInterface
{
    /**
     * Registry of available validation rules.
     *
     * @var RuleRegistryInterface
     */
    private RuleRegistryInterface $ruleRegistry;

    /**
     * Current validation context.
     *
     * @var ContextInterface|null
     */
    private ?ContextInterface $context = null;

    /**
     * Custom validator extensions.
     *
     * @var array<string, callable|string>
     */
    private array $extensions = [];

    /**
     * Creates a new validator instance.
     *
     * @param RuleRegistryInterface $ruleRegistry Registry of available validation rules
     */
    public function __construct(RuleRegistryInterface $ruleRegistry)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    /**
     * Validates data against rules.
     *
     * @param array<string, mixed> $data The data to validate
     * @param array<string, mixed>|string $rules The validation rules
     * @param array<string, string> $messages Custom error messages
     * @return ValidationResultInterface The validation result
     */
    public function validate(array $data, array|string $rules, array $messages = []): ValidationResultInterface
    {
        $compiledRules = $this->compile($rules);

        $validationResult = new ValidationResult();

        $validData = [];

        $contextMessages = [];
        if ($this->context !== null) {
            $contextMessages = $this->context->getMessages();
        }

        $allMessages = array_merge($contextMessages, $messages);

        foreach ($compiledRules->getCompiledRules() as $field => $fieldRules) {
            if ($field === '_default' && count($compiledRules->getCompiledRules()) > 1) {
                continue;
            }

            $actualField = $field === '_default' ? array_key_first($data) : $field;

            $value = $this->getValueForField($actualField, $data);

            $errors = $this->validateField($actualField, $value, $fieldRules, $data, $allMessages);

            if (! empty($errors)) {
                foreach ($errors as $error) {
                    $validationResult->addError($actualField, $error);
                }
            } else {
                if (array_key_exists($actualField, $data) || $this->getValueForField($actualField, $data) !== null) {
                    $validData[$actualField] = $value;
                }
            }
        }

        foreach ($validData as $field => $value) {
            if (! $validationResult->hasError($field)) {
                $validationResult->addValidData($field, $value);
            }
        }

        return $validationResult;
    }

    /**
     * Adds a validation rule to the validator.
     *
     * @param string $name The rule name
     * @param callable|RuleInterface $rule The rule implementation
     * @return self The current instance for method chaining
     * @throws \InvalidArgumentException If the rule is a callable but not a RuleInterface
     */
    public function addRule(string $name, callable|RuleInterface $rule): self
    {
        if (is_callable($rule) && ! ($rule instanceof RuleInterface)) {
            throw new \InvalidArgumentException("Callable rules are not supported yet. Please provide a RuleInterface instance.");
        }

        if ($rule instanceof RuleInterface) {
            $this->ruleRegistry->register($name, $rule);
        }

        return $this;
    }

    /**
     * Sets the validation context.
     *
     * @param ContextInterface $context The validation context
     * @return self The current instance for method chaining
     */
    public function setContext(ContextInterface $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Compiles rules into a normalized format.
     *
     * @param array<string, mixed>|string $rules The rules to compile
     * @return CompiledRulesInterface The compiled rules
     */
    public function compile(array|string $rules): CompiledRulesInterface
    {
        if (is_string($rules)) {
            return new CompiledRules(['_default' => $rules]);
        }

        if ($this->context !== null) {
            $contextRules = $this->context->getRules();
            if (! empty($contextRules)) {
                $rules = array_merge($contextRules, $rules);
            }
        }

        return new CompiledRules($rules);
    }

    /**
     * Extends the validator with a custom rule or extension.
     *
     * @param string $name The extension name
     * @param callable|string $extension The extension implementation
     * @return self The current instance for method chaining
     */
    public function extend(string $name, callable|string $extension): self
    {
        $this->extensions[$name] = $extension;

        return $this;
    }

    /**
     * Gets a rule by name.
     *
     * @param string $name The rule name
     * @return RuleInterface|null The rule or null if not found
     */
    public function getRule(string $name): ?RuleInterface
    {
        return $this->ruleRegistry->get($name);
    }

    /**
     * Checks if a rule exists.
     *
     * @param string $name The rule name
     * @return bool True if the rule exists, false otherwise
     */
    public function hasRule(string $name): bool
    {
        return $this->ruleRegistry->has($name);
    }

    /**
     * Gets all available rules.
     *
     * @return array<string, RuleInterface> All available rules indexed by name
     */
    public function getAvailableRules(): array
    {
        return $this->ruleRegistry->all();
    }

    /**
     * Validates a single field against its rules.
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array<array<string, mixed>> $rules The rules for this field
     * @param array<string, mixed> $data All data being validated
     * @param array<string, string> $messages Custom error messages
     * @return array<string> Array of error messages for this field
     */
    private function validateField(string $field, mixed $value, array $rules, array $data, array $messages = []): array
    {
        $errors = [];

        foreach ($rules as $rule) {
            $errorMessage = $this->validateRule($field, $value, $rule, $data, $messages);
            if ($errorMessage !== null) {
                $errors[] = $errorMessage;

                break;
            }
        }

        return $errors;
    }

    /**
     * Validates a value against a specific rule.
     *
     * @param string $field The field name
     * @param mixed $value The value to validate
     * @param array<string, mixed> $rule The rule configuration
     * @param array<string, mixed> $data All data being validated
     * @param array<string, string> $messages Custom error messages
     * @return string|null Error message if validation fails, null otherwise
     * @throws \InvalidArgumentException If the rule is not registered
     */
    private function validateRule(string $field, mixed $value, array $rule, array $data, array $messages = []): ?string
    {
        $ruleName = $rule['name'];
        $parameters = $rule['parameters'] ?? [];

        if (isset($this->extensions[$ruleName])) {
            $extension = $this->extensions[$ruleName];

            if (is_callable($extension)) {
                $valid = $extension($value, $parameters, $data);
                if (! $valid) {
                    return $this->getErrorMessage($field, $ruleName, $parameters, $messages);
                }

                return null;
            }
        }

        $ruleInstance = $this->ruleRegistry->get($ruleName);

        if (! $ruleInstance) {
            throw new \InvalidArgumentException("Rule '{$ruleName}' is not registered.");
        }

        $ruleInstance->setParameters($parameters);

        $valid = $ruleInstance->validate($value, $parameters, $data);

        if (! $valid) {
            return $this->getErrorMessage($field, $ruleName, $parameters, $messages, $ruleInstance->getMessage());
        }

        return null;
    }

    /**
     * Gets the appropriate error message for a failed validation.
     *
     * @param string $field The field name
     * @param string $ruleName The rule name
     * @param array<mixed> $parameters The rule parameters
     * @param array<string, string> $customMessages Custom error messages
     * @param string|null $defaultMessage Default error message
     * @return string The formatted error message
     */
    private function getErrorMessage(string $field, string $ruleName, array $parameters, array $customMessages, ?string $defaultMessage = null): string
    {
        $messageKey = "{$field}.{$ruleName}";

        $message = $customMessages[$messageKey] ?? $customMessages[$ruleName] ?? $defaultMessage ?? "The {$field} field validation failed.";

        return $this->formatMessage($message, $field, $parameters);
    }

    /**
     * Formats an error message with field name and parameters.
     *
     * @param string $message The message template
     * @param string $field The field name
     * @param array<mixed> $parameters The parameters to insert
     * @return string The formatted message
     */
    protected function formatMessage(string $message, string $field, array $parameters = []): string
    {
        $message = str_replace(':attribute', $field, $message);

        foreach ($parameters as $key => $value) {
            if (is_string($key)) {
                $message = str_replace(":{$key}", (string)$value, $message);
            } else {
                $message = str_replace(":param{$key}", (string)$value, $message);
            }
        }

        return $message;
    }

    /**
     * Gets a value from data using dot notation.
     *
     * @param string $field The field name (can use dot notation)
     * @param array<string, mixed> $data The data to extract from
     * @return mixed The field value or null if not found
     */
    private function getValueForField(string $field, array $data): mixed
    {
        if (str_contains($field, '.')) {
            $segments = explode('.', $field);
            $current = $data;

            foreach ($segments as $segment) {
                if (! isset($current[$segment])) {
                    return null;
                }
                $current = $current[$segment];
            }

            return $current;
        }

        return $data[$field] ?? null;
    }
}
