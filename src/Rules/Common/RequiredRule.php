<?php

namespace ValidatorPro\Rules\Common;

use ValidatorPro\Rules\AbstractRule;

/**
 * Rule to validate that a value is present and not empty.
 *
 * This rule validates that a value is not null, not an empty string,
 * and not an empty array. This is typically used as a foundational
 * rule to ensure required fields are provided.
 */
class RequiredRule extends AbstractRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'Le champ :attribute est obligatoire';

    /**
     * Validates if the value is present and not empty.
     *
     * The validation fails if:
     * - The value is null
     * - The value is an empty string (after trimming)
     * - The value is an empty array
     *
     * @param mixed $value The value to validate
     * @param array<string, mixed> $parameters Optional parameters (not used by this rule)
     * @param array<string, mixed> $data Optional data context (not used by this rule)
     * @return bool True if validation passes, false otherwise
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_array($value) && count($value) === 0) {
            return false;
        }

        return true;
    }
}
