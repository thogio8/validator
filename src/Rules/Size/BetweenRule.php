<?php

namespace ValidatorPro\Rules\Size;

use InvalidArgumentException;

/**
 * Rule to validate that a value's size is between a minimum and maximum.
 *
 * This rule checks if the size of a value (string length, array count,
 * or numeric value) is between a specified minimum and maximum (inclusive).
 */
class BetweenRule extends AbstractSizeRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be between :param0 and :param1';

    /**
     * Determines if the size is between the minimum and maximum.
     *
     * @param int|float $size The size to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return bool True if the size is valid, false otherwise
     * @throws InvalidArgumentException If min or max parameters are not provided
     */
    public function isSizeValid(int|float $size, array $parameters = []): bool
    {
        if (empty($parameters[0]) || ! isset($parameters[1])) {
            throw new InvalidArgumentException('Both minimum and maximum parameters are required for between rule');
        }

        $min = is_numeric($parameters[0]) ? (float)$parameters[0] : 0;
        $max = is_numeric($parameters[1]) ? (float)$parameters[1] : 0;

        if ($min > $max) {
            throw new InvalidArgumentException('Minimum parameter cannot be greater than maximum parameter');
        }

        return $size >= $min && $size <= $max;
    }
}
