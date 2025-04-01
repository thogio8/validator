<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for validation rules.
 *
 * This interface defines the contract for validation rule objects
 * that can validate values against specific criteria, provide error messages,
 * and manage parameters.
 */
interface RuleInterface
{
    /**
     * Validates a value against this rule.
     *
     * @param mixed $value The value to validate
     * @param array<string, mixed> $parameters Optional parameters for the validation
     * @param array<string, mixed> $data Optional additional data for context
     * @return bool True if validation passes, false otherwise
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool;

    /**
     * Gets the error message for this rule.
     *
     * @return string The error message template
     */
    public function getMessage(): string;

    /**
     * Gets the name of this rule.
     *
     * @return string The rule name
     */
    public function getName(): string;

    /**
     * Sets parameters for this rule.
     *
     * @param array<int|string, mixed> $parameters The parameters to set
     * @return self The current instance for method chaining
     */
    public function setParameters(array $parameters): self;

    /**
     * Gets the current parameters for this rule.
     *
     * @return array<int|string, mixed> The current parameters
     */
    public function getParameters(): array;

    /**
     * Sets a custom error message for this rule.
     *
     * @param string|null $message The custom error message or null to use default
     * @return self The current instance for method chaining
     */
    public function setErrorMessage(?string $message): self;

    /**
     * Checks if this rule supports a specific data type.
     *
     * @param string $type The data type to check
     * @return bool True if this rule supports the type, false otherwise
     */
    public function supportsType(string $type): bool;

    /**
     * Gets documentation for this rule.
     *
     * @return array<string, mixed> Documentation array with keys like 'name', 'description', 'parameters', 'examples'
     */
    public function getDocumentation(): array;
}
