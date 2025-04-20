<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

/**
 * Rule to validate that a value is of type string.
 *
 * This rule checks if the provided value is a valid string according to PHP's type system.
 */
class StringRule extends AbstractRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be a string.';

    /**
     * Validates if the value is a string.
     *
     * @param mixed $value The value to validate
     * @param array<string, mixed> $parameters Optional parameters (not used by this rule)
     * @param array<string, mixed> $data Optional data context (not used by this rule)
     * @return bool True if validation passes, false otherwise
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_string($value);
    }
}
