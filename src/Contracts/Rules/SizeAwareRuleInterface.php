<?php

namespace ValidatorPro\Contracts\Rules;

use ValidatorPro\Contracts\RuleInterface;

/**
 * Interface for validation rules that work with the concept of "size".
 *
 * This interface extends the base RuleInterface and adds methods
 * specific to rules that need to determine and compare sizes of values.
 */
interface SizeAwareRuleInterface extends RuleInterface
{
    /**
     * Gets the size of a value.
     *
     * @param mixed $value The value to get the size of
     * @return int|float|null The size of the value or null if size cannot be determined
     */
    public function getSize(mixed $value): int|float|null;

    /**
     * Determines if the value's size is valid according to the rule's criteria.
     *
     * @param int|float $size The size to validate
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return bool True if the size is valid, false otherwise
     */
    public function isSizeValid(int|float $size, array $parameters = []): bool;
}
