<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for validation strategies.
 *
 * This interface defines how validation should be performed,
 * allowing for different strategies like "stop on first error",
 * "validate all fields", etc.
 */
interface ValidationStrategyInterface
{
    /**
     * Validates data against rules using this strategy.
     *
     * @param array<string, mixed> $data The data to validate
     * @param CompiledRulesInterface $rules The compiled validation rules
     * @param array<string, string> $messages Custom error messages
     * @return ValidationResultInterface The validation result
     */
    public function validate(
        array $data,
        CompiledRulesInterface $rules,
        array $messages = []
    ): ValidationResultInterface;
}
