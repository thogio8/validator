<?php

namespace ValidatorPro\Rules\Size;

use InvalidArgumentException;

/**
 * Rule to validate that a value has a minimum size.
 *
 * This rule checks if the size of a value (string length, array count,
 * or numeric value) is greater than or equal to a specified minimum.
 */
class MinRule extends AbstractSizeRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be at least :param0';

    /**
     * Determines if the size is greater than or equal to the minimum.
     *
     * @param int|float $size The size to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return bool True if the size is valid, false otherwise
     * @throws InvalidArgumentException If no minimum parameter is provided
     */
    public function isSizeValid(int|float $size, array $parameters = []): bool
    {
        if (empty($parameters[0])) {
            throw new InvalidArgumentException('Minimum parameter is required for min rule');
        }

        $min = is_numeric($parameters[0]) ? (float)$parameters[0] : 0;

        return $size >= $min;
    }
}
