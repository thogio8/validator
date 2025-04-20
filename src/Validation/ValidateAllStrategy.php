<?php

namespace ValidatorPro\Validation;

/**
 * Validation strategy that validates all rules for all fields.
 *
 * This strategy continues validation even after errors are encountered,
 * collecting all validation errors.
 */
class ValidateAllStrategy extends AbstractValidationStrategy
{
    /**
     * Validates a single field against all its rules, collecting all errors.
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array<array<string, mixed>> $rules The rules for this field
     * @param array<string, mixed> $data All data being validated
     * @param array<string, string> $messages Custom error messages
     * @return array<string> Array of error messages for this field
     */
    protected function validateField(
        string $field,
        mixed $value,
        array $rules,
        array $data,
        array $messages = []
    ): array {
        $errors = [];

        foreach ($rules as $rule) {
            $errorMessage = $this->validateRule($field, $value, $rule, $data, $messages);
            if ($errorMessage !== null) {
                $errors[] = $errorMessage;
                // No break - continue validating all rules
            }
        }

        return $errors;
    }
}
