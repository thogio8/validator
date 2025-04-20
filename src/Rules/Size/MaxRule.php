<?php

namespace ValidatorPro\Rules\Size;

use InvalidArgumentException;

/**
 * Rule to validate that a value has a maximum size.
 *
 * This rule checks if the size of a value (string length, array count,
 * or numeric value) is less than or equal to a specified maximum.
 */
class MaxRule extends AbstractSizeRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute may not be greater than :param0';

    /**
     * Determines if the size is less than or equal to the maximum.
     *
     * @param int|float $size The size to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return bool True if the size is valid, false otherwise
     * @throws InvalidArgumentException If no maximum parameter is provided
     */
    public function isSizeValid(int|float $size, array $parameters = []): bool
    {
        if (empty($parameters[0])) {
            throw new InvalidArgumentException('Maximum parameter is required for max rule');
        }

        $max = is_numeric($parameters[0]) ? (float)$parameters[0] : 0;

        return $size <= $max;
    }
}
