<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for compiled validation rules.
 *
 * This interface defines the contract for objects that store
 * and manage compiled validation rules in a normalized format
 * ready for execution.
 */
interface CompiledRulesInterface
{
    /**
     * Gets the original uncompiled rules.
     *
     * @return array<string, mixed> The original rules
     */
    public function getRules(): array;

    /**
     * Gets the compiled rules.
     *
     * @return array<string, array<mixed>> The compiled rules
     */
    public function getCompiledRules(): array;

    /**
     * Adds a rule for a field.
     *
     * @param string $field The field name
     * @param mixed $rule The rule to add
     * @return self The current instance for method chaining
     */
    public function addRule(string $field, mixed $rule): self;

    /**
     * Checks if a field has rules.
     *
     * @param string $field The field name
     * @return bool True if the field has rules, false otherwise
     */
    public function hasRule(string $field): bool;

    /**
     * Gets rules for a field.
     *
     * @param string $field The field name
     * @return array<mixed> The rules for the field
     */
    public function getRule(string $field): array;
}
