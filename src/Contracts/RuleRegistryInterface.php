<?php

namespace ValidatorPro\Contracts;

use InvalidArgumentException;

/**
 * Interface for validation rule registry.
 *
 * This interface defines the contract for registry objects that store
 * and manage available validation rules for the validator.
 */
interface RuleRegistryInterface
{
    /**
     * Registers a rule in the registry.
     *
     * @param string $name The name of the rule
     * @param RuleInterface $rule The rule instance
     * @return self The current instance for method chaining
     * @throws InvalidArgumentException If the rule name is empty
     */
    public function register(string $name, RuleInterface $rule): self;

    /**
     * Retrieves a rule by its name.
     *
     * @param string $name The name of the rule to retrieve
     * @return RuleInterface|null The rule instance or null if not found
     */
    public function get(string $name): ?RuleInterface;

    /**
     * Checks if a rule exists in the registry.
     *
     * @param string $name The name of the rule to check
     * @return bool True if the rule exists, false otherwise
     */
    public function has(string $name): bool;

    /**
     * Retrieves all available rules in the registry.
     *
     * @return array<string, RuleInterface> All rules indexed by their name
     */
    public function all(): array;
}