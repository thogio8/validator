<?php

namespace ValidatorPro\Rules\Size;

use ValidatorPro\Contracts\Rules\SizeAwareRuleInterface;
use ValidatorPro\Rules\AbstractRule;

/**
 * Abstract base class for size-related validation rules.
 *
 * This class provides common functionality for validation rules that
 * work with the concept of "size" (length of strings, count of arrays,
 * magnitude of numbers, etc.).
 */
abstract class AbstractSizeRule extends AbstractRule implements SizeAwareRuleInterface
{
    /**
     * Validates if the value's size is valid according to the rule.
     *
     * @param mixed $value The value to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @param array<string, mixed> $data Additional data
     * @return bool True if validation passes, false otherwise
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        $size = $this->getSize($value);

        if ($size === null) {
            return false;
        }

        return $this->isSizeValid($size, $parameters);
    }

    /**
     * Gets the size of a value based on its type.
     *
     * @param mixed $value The value to get the size of
     * @return int|float|null The size of the value or null if size cannot be determined
     */
    public function getSize(mixed $value): int|float|null
    {
        if (is_string($value)) {
            return mb_strlen($value);
        }

        if (is_array($value)) {
            return count($value);
        }

        if (is_numeric($value)) {
            return (float)$value;
        }

        return null;
    }

    /**
     * Determines if the value's size is valid according to the rule's criteria.
     * This method must be implemented by concrete rule classes.
     *
     * @param int|float $size The size to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return bool True if the size is valid, false otherwise
     */
    abstract public function isSizeValid(int|float $size, array $parameters = []): bool;
}
