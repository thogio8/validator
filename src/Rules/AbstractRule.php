<?php

namespace ValidatorPro\Rules;

use ValidatorPro\Contracts\RuleInterface;

/**
 * Abstract base class for validation rules.
 *
 * This class provides common functionality for validation rules,
 * including parameter management, error messages, and naming conventions.
 * Specific validation rules should extend this class and implement
 * the validate method.
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * Parameters for the validation rule.
     *
     * @var array<int|string, mixed> Rule parameters
     */
    protected array $parameters = [];

    /**
     * Default error message for the validation rule.
     *
     * @var string Default error message template
     */
    protected string $message = 'Validation failed for :attribute';

    /**
     * Creates a new rule instance.
     *
     * @param array<int|string, mixed> $parameters Initial parameters for the rule
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Gets the validation error message.
     *
     * @return string The error message template
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Gets the rule name by extracting it from the class name.
     *
     * Converts the class name (without namespace and "Rule" suffix)
     * to snake_case for the rule name.
     *
     * @return string The rule name in snake_case
     */
    public function getName(): string
    {
        // Extract class name without namespace
        $className = get_class($this);
        $parts = explode('\\', $className);
        $simpleName = end($parts);

        // Remove "Rule" suffix and convert to snake_case
        return $this->camelToSnake(str_replace('Rule', '', $simpleName));
    }

    /**
     * Gets parameters for this rule.
     *
     * @return array<int|string, mixed> The current parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Sets parameters for this rule.
     *
     * @param array<int|string, mixed> $parameters The parameters to set
     * @return self The current instance for method chaining
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Sets custom error message.
     *
     * @param string|null $message The custom error message or null to use default
     * @return self The current instance for method chaining
     */
    public function setErrorMessage(?string $message): self
    {
        if ($message !== null) {
            $this->message = $message;
        }

        return $this;
    }

    /**
     * Checks if this rule supports a specific data type.
     *
     * By default, rules support all types. Override in specific rules
     * to restrict to certain types.
     *
     * @param string $type The data type to check
     * @return bool True if this rule supports the type, false otherwise
     */
    public function supportsType(string $type): bool
    {
        return true; // By default, rules support all types, override in specific rules
    }

    /**
     * Gets documentation for this rule.
     *
     * @return array<string, mixed> Documentation array with keys like 'name', 'description', 'parameters', 'examples'
     */
    public function getDocumentation(): array
    {
        return [
            'name' => $this->getName(),
            'description' => 'Documentation for ' . $this->getName() . ' rule',
            'parameters' => [],
            'examples' => [],
        ];
    }

    /**
     * Converts camelCase to snake_case.
     *
     * @param string $input String in camelCase format
     * @return string String in snake_case format
     */
    protected function camelToSnake(string $input): string
    {
        if ($input === '') {
            return '';
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
