<?php

namespace ValidatorPro\Contracts\Rules;

/**
 * Interface for rules that require parameters to function.
 *
 * This interface defines methods specific to validation rules that
 * need parameters to perform their validation logic.
 */
interface ParameterizedRuleInterface
{
    /**
     * Validates if a rule's parameters are valid.
     *
     * @param array<int|string, mixed> $parameters The parameters to validate
     * @return bool True if parameters are valid, false otherwise
     */
    public function validateParameters(array $parameters): bool;

    /**
     * Gets the minimum number of parameters required by this rule.
     *
     * @return int The minimum number of required parameters
     */
    public function getMinParametersCount(): int;

    /**
     * Gets the maximum number of parameters allowed by this rule.
     *
     * @return int|null The maximum number of parameters, or null if unlimited
     */
    public function getMaxParametersCount(): ?int;
}
