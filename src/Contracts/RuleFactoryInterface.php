<?php

namespace ValidatorPro\Contracts;

use InvalidArgumentException;

/**
 * Interface for rule factories.
 *
 * This interface defines methods for creating rule instances dynamically,
 * which is useful for extending the validator with custom rules.
 */
interface RuleFactoryInterface
{
    /**
     * Creates a rule instance from a name and parameters.
     *
     * @param string $name The rule name
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return RuleInterface The created rule instance
     * @throws InvalidArgumentException If the rule cannot be created
     */
    public function createRule(string $name, array $parameters = []): RuleInterface;

    /**
     * Registers a rule creator function.
     *
     * @param string $name The rule name
     * @param callable $creator A function that creates the rule
     * @return self The current instance for method chaining
     */
    public function registerRuleCreator(string $name, callable $creator): self;

    /**
     * Checks if a rule creator exists.
     *
     * @param string $name The rule name
     * @return bool True if a creator exists, false otherwise
     */
    public function hasRuleCreator(string $name): bool;
}
