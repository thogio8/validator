<?php

namespace ValidatorPro\Contracts\Rules;

use ValidatorPro\Contracts\RuleInterface;

/**
 * Interface for validation rules that perform comparisons.
 *
 * This interface extends the base RuleInterface and adds methods
 * specific to rules that compare values against thresholds or other values.
 */
interface ComparableRuleInterface extends RuleInterface
{
    /**
     * Compares a value against a threshold.
     *
     * @param mixed $value The value to compare
     * @param mixed $threshold The threshold to compare against
     * @return int -1 if value < threshold, 0 if equal, 1 if value > threshold
     */
    public function compare(mixed $value, mixed $threshold): int;

    /**
     * Gets the comparable value from a mixed input.
     *
     * @param mixed $value The input value
     * @return mixed The comparable value extracted from the input
     */
    public function getComparableValue(mixed $value): mixed;
}
