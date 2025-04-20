<?php

namespace ValidatorPro\Validation;

/**
 * Validation strategy that stops on the first error for each field.
 *
 * This strategy validates each field until the first error is encountered,
 * then moves on to the next field.
 */
class StopOnFirstErrorStrategy extends AbstractValidationStrategy
{
    /**
     * Validates a single field against its rules, stopping on first error.
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

                break; // Stop on first error
            }
        }

        return $errors;
    }
}
