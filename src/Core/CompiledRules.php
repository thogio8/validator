<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\CompiledRulesInterface;

/**
 * Class for storing and managing compiled validation rules.
 *
 * This class implements CompiledRulesInterface and provides methods
 * to manage validation rules in a normalized format ready for execution.
 */
class CompiledRules implements CompiledRulesInterface
{
    /**
     * Original uncompiled rules.
     *
     * @var array<string, mixed>
     */
    private array $rules = [];

    /**
     * Compiled rules.
     *
     * @var array<string, array<mixed>>
     */
    private array $compiledRules = [];

    /**
     * Creates a new instance of compiled rules.
     *
     * @param array<string, mixed> $rules Uncompiled rules
     * @param array<string, array<mixed>> $compiledRules Pre-existing compiled rules (optional)
     */
    public function __construct(array $rules = [], array $compiledRules = [])
    {
        $this->rules = $rules;
        $this->compiledRules = $compiledRules;

        // Only compile if we have rules to compile and no pre-compiled rules
        if (! empty($rules) && empty($compiledRules)) {
            $this->compileInitialRules();
        }
    }

    /**
     * Gets the original uncompiled rules.
     *
     * @return array<string, mixed> The original rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Gets the compiled rules.
     *
     * @return array<string, array<mixed>> The compiled rules
     */
    public function getCompiledRules(): array
    {
        return $this->compiledRules;
    }

    /**
     * Adds a rule for a field.
     *
     * @param string $field The field name
     * @param mixed $rule The rule to add
     * @return self The current instance for method chaining
     */
    public function addRule(string $field, mixed $rule): self
    {
        // Update raw rules
        if (! isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }

        // If rules is already an array, append to it, otherwise set it directly
        if (is_array($this->rules[$field])) {
            $this->rules[$field][] = $rule;
        } else {
            $this->rules[$field] = $rule;
        }

        // Update compiled rules
        if (! isset($this->compiledRules[$field])) {
            $this->compiledRules[$field] = [];
        }

        // Add to compiled rules
        if (is_string($rule)) {
            // Parse string rule like "required|email"
            $parsedRules = $this->parseRuleString($rule);
            foreach ($parsedRules as $parsedRule) {
                $this->compiledRules[$field][] = $parsedRule;
            }
        } elseif (is_array($rule)) {
            // If it's already an array, add it directly if it has the right format
            // Otherwise, convert it to the right format
            if (isset($rule['name'])) {
                $this->compiledRules[$field][] = $rule;
            } else {
                foreach ($rule as $singleRule) {
                    if (is_string($singleRule)) {
                        $parsedRules = $this->parseRuleString($singleRule);
                        foreach ($parsedRules as $parsedRule) {
                            $this->compiledRules[$field][] = $parsedRule;
                        }
                    } elseif (is_array($singleRule) && isset($singleRule['name'])) {
                        // It's already in the correct format
                        $this->compiledRules[$field][] = $singleRule;
                    } else {
                        // Handle other cases
                        $this->compiledRules[$field][] = [
                            'name' => is_object($singleRule) && method_exists($singleRule, '__toString')
                                ? (string)$singleRule
                                : 'unknown',
                            'parameters' => [],
                        ];
                    }
                }
            }
        } else {
            // Handle non-string, non-array rule (like objects)
            $this->compiledRules[$field][] = [
                'name' => is_object($rule) && method_exists($rule, '__toString')
                    ? (string)$rule
                    : 'unknown',
                'parameters' => [],
            ];
        }

        return $this;
    }

    /**
     * Checks if a field has rules.
     *
     * @param string $field The field name
     * @return bool True if the field has rules, false otherwise
     */
    public function hasRule(string $field): bool
    {
        return isset($this->compiledRules[$field]) && ! empty($this->compiledRules[$field]);
    }

    /**
     * Gets rules for a field.
     *
     * @param string $field The field name
     * @return array<mixed> The rules for the field
     */
    public function getRule(string $field): array
    {
        return $this->compiledRules[$field] ?? [];
    }

    /**
     * Compiles all raw rules into the normalized format during initialization.
     *
     * This method is protected for testing purposes.
     *
     * @return void
     */
    protected function compileInitialRules(): void
    {
        foreach ($this->rules as $field => $rule) {
            $this->addRule($field, $rule);
        }
    }

    /**
     * Parses a rule string into an array of rule definitions.
     *
     * @param string $ruleString Rule string like "required|email|min:8"
     * @return array<array<string, mixed>> Parsed rules
     */
    private function parseRuleString(string $ruleString): array
    {
        $rules = [];
        $ruleSegments = explode('|', $ruleString);

        foreach ($ruleSegments as $segment) {
            if (empty($segment)) {
                continue;
            }

            // Split rule name and parameters (e.g., "min:8")
            if (str_contains($segment, ':')) {
                // Limit to 2 to handle cases like regex patterns that contain colons
                [$name, $paramString] = explode(':', $segment, 2);
                $parameters = explode(',', $paramString);
            } else {
                $name = $segment;
                $parameters = [];
            }

            $rules[] = [
                'name' => $name,
                'parameters' => $parameters,
            ];
        }

        return $rules;
    }
}
