<?php

namespace ValidatorPro\Rules\String;

use ValidatorPro\Rules\AbstractRule;

/**
 * Rule to validate email addresses.
 *
 * This rule ensures that a value is a properly formatted email address
 * using PHP's built-in email validation filter.
 */
class EmailRule extends AbstractRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be a valid email address';

    /**
     * Validates if the value is a valid email address.
     *
     * @param mixed $value The value to validate
     * @param array<string, mixed> $parameters Optional parameters (not used by this rule)
     * @param array<string, mixed> $data Optional data context (not used by this rule)
     * @return bool True if validation passes, false otherwise
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        // Check if the value is a string
        if (! is_string($value)) {
            return false;
        }

        // Use PHP's email validation filter
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
