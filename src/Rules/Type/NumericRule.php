<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

/**
 * Rule to validate that a value is numeric.
 *
 * This rule checks if the provided value is a valid number (integer, float or numeric string)
 * according to PHP's is_numeric() function.
 */
class NumericRule extends AbstractRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be a number.';

    /**
     * Validates if the value is numeric.
     *
     * @param mixed $value The value to validate
     * @param array<string, mixed> $parameters Optional parameters (not used by this rule)
     * @param array<string, mixed> $data Optional data context (not used by this rule)
     * @return bool True if validation passes, false otherwise
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_numeric($value);
    }
}
