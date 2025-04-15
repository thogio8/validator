<?php

namespace ValidatorPro\Contracts;

/**
 * Interface defining the contract for validation results.
 *
 * This interface defines methods to check validation status, retrieve errors,
 * and manage validated data after a validation operation has been performed.
 */
interface ValidationResultInterface
{
    /**
     * Determines if the validation passed (no errors).
     *
     * @return bool True if validation passed, false otherwise
     */
    public function passes(): bool;

    /**
     * Determines if the validation failed (has errors).
     *
     * @return bool True if validation failed, false otherwise
     */
    public function fails(): bool;

    /**
     * Gets all validation errors.
     *
     * @return array<string, array<string>> All validation errors indexed by field name
     */
    public function getErrors(): array;

    /**
     * Gets all valid data that passed validation.
     *
     * @return array<string, mixed> Valid data indexed by field name
     */
    public function getValidData(): array;

    /**
     * Gets the first error message for each field that has errors.
     *
     * @return array<string, string> First error message for each field indexed by field name
     */
    public function getFirstErrors(): array;

    /**
     * Gets the first error message for a specific field.
     *
     * @param string $field The field name to get the error for
     * @return string|null First error message for the field or null if no errors
     */
    public function getError(string $field): ?string;

    /**
     * Checks if a field has any validation errors.
     *
     * @param string $field The field name to check
     * @return bool True if the field has errors, false otherwise
     */
    public function hasError(string $field): bool;

    /**
     * Gets all error messages for a specific field.
     *
     * @param string $field The field name to get errors for
     * @return array<string> Array of error messages for the field
     */
    public function getFieldErrors(string $field): array;

    /**
     * Gets the total count of all error messages across all fields.
     *
     * @return int Total number of error messages
     */
    public function getErrorCount(): int;

    /**
     * Adds an error message for a specific field.
     *
     * @param string $field The field name to add an error for
     * @param string $message The error message to add
     * @return self The current instance for method chaining
     */
    public function addError(string $field, string $message): self;

    /**
     * Gets the list of all fields that have been validated.
     *
     * @return array<string> Array of validated field names
     */
    public function getValidatedFields(): array;

    /**
     * Adds a valid data field to the validation result.
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @return self The current instance for method chaining
     */
    public function addValidData(string $field, mixed $value): self;
}
