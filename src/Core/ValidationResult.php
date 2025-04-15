<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\ValidationResultInterface;

/**
 * ValidationResult class that stores and manages validation results.
 *
 * This class implements ValidationResultInterface and provides methods
 * to check validation status, retrieve errors, and manage validated data.
 */
class ValidationResult implements ValidationResultInterface
{
    /**
     * Validation errors organized by field.
     *
     * @var array<string, array<string>> Array of validation error messages indexed by field name
     */
    private array $errors = [];

    /**
     * Valid data that passed validation.
     *
     * @var array<string, mixed> Array of validated data indexed by field name
     */
    private array $validData = [];

    /**
     * List of fields that have been validated.
     *
     * @var array<string> Array of field names that have been validated
     */
    private array $validatedFields = [];

    /**
     * Creates a new validation result instance.
     *
     * @param array<string, array<string>> $errors Initial validation errors indexed by field name
     * @param array<string, mixed> $validData Initial valid data indexed by field name
     * @param array<string> $validatedFields Initial list of validated field names
     */
    public function __construct(
        array $errors = [],
        array $validData = [],
        array $validatedFields = []
    ) {
        $this->errors = $errors;
        $this->validData = $validData;
        $this->validatedFields = $validatedFields;
    }

    /**
     * Determines if the validation passed (no errors).
     *
     * @return bool True if validation passed, false otherwise
     */
    public function passes(): bool
    {
        if (count($this->errors) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the validation failed (has errors).
     *
     * @return bool True if validation failed, false otherwise
     */
    public function fails(): bool
    {
        if (count($this->errors) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Gets all validation errors.
     *
     * @return array<string, array<string>> All validation errors indexed by field name
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Gets all valid data that passed validation.
     *
     * @return array<string, mixed> Valid data indexed by field name
     */
    public function getValidData(): array
    {
        return $this->validData;
    }

    /**
     * Gets the first error message for each field that has errors.
     *
     * @return array<string, string> First error message for each field indexed by field name
     */
    public function getFirstErrors(): array
    {
        $result = [];
        foreach ($this->errors as $field => $errorMessages) {
            if (! empty($errorMessages)) {
                $result[$field] = $errorMessages[0];
            }
        }

        return $result;
    }

    /**
     * Gets the first error message for a specific field.
     *
     * @param string $field The field name to get the error for
     * @return string|null First error message for the field or null if no errors
     */
    public function getError(string $field): ?string
    {
        if (isset($this->errors[$field]) && ! empty($this->errors[$field])) {
            return $this->errors[$field][0];
        }

        return null;
    }

    /**
     * Checks if a field has any validation errors.
     *
     * @param string $field The field name to check
     * @return bool True if the field has errors, false otherwise
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && ! empty($this->errors[$field]);
    }

    /**
     * Gets all error messages for a specific field.
     *
     * @param string $field The field name to get errors for
     * @return array<string> Array of error messages for the field
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Gets the total count of all error messages across all fields.
     *
     * @return int Total number of error messages
     */
    public function getErrorCount(): int
    {
        $result = 0;
        foreach ($this->errors as $field => $errorMessages) {
            $result += count($errorMessages);
        }

        return $result;
    }

    /**
     * Adds an error message for a specific field.
     *
     * @param string $field The field name to add an error for
     * @param string $message The error message to add
     * @return self The current instance for method chaining
     */
    public function addError(string $field, string $message): self
    {
        if (! isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;

        if (! in_array($field, $this->validatedFields)) {
            $this->validatedFields[] = $field;
        }

        return $this;
    }

    /**
     * Gets the list of all fields that have been validated.
     *
     * @return array<string> Array of validated field names
     */
    public function getValidatedFields(): array
    {
        return $this->validatedFields;
    }

    /**
     * Adds a valid data field to the validation result.
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @return self The current instance for method chaining
     */
    public function addValidData(string $field, mixed $value): self
    {
        $this->validData[$field] = $value;

        return $this;
    }
}
