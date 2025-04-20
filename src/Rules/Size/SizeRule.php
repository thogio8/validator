<?php

namespace ValidatorPro\Rules\Size;

use InvalidArgumentException;

/**
 * Rule to validate that a value has an exact size.
 *
 * This rule checks if the size of a value (string length, array count,
 * or numeric value) is exactly equal to a specified size.
 */
class SizeRule extends AbstractSizeRule
{
    /**
     * The error message for this validation rule.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be :param0';

    /**
     * Determines if the size is exactly equal to the expected size.
     *
     * @param int|float $size The size to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return bool True if the size is valid, false otherwise
     * @throws InvalidArgumentException If no size parameter is provided
     */
    public function isSizeValid(int|float $size, array $parameters = []): bool
    {
        if (empty($parameters[0])) {
            throw new InvalidArgumentException('Size parameter is required for size rule');
        }

        $expectedSize = is_numeric($parameters[0]) ? (float)$parameters[0] : 0;

        // Use a small epsilon for float comparison to handle floating point precision issues
        if (is_float($size) || is_float($expectedSize)) {
            return abs($size - $expectedSize) < PHP_FLOAT_EPSILON;
        }

        return $size === $expectedSize;
    }
}
