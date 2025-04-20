<?php

namespace ValidatorPro\Factories;

use InvalidArgumentException;
use ValidatorPro\Contracts\RuleFactoryInterface;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;

/**
 * Factory for creating rule instances.
 *
 * This class provides a way to dynamically create rule instances
 * based on names and parameters, supporting custom rule creation.
 */
class RuleFactory implements RuleFactoryInterface
{
    /**
     * Registry of existing rules.
     *
     * @var RuleRegistryInterface
     */
    private RuleRegistryInterface $ruleRegistry;

    /**
     * Map of rule name to creator callables.
     *
     * @var array<string, callable>
     */
    private array $creators = [];

    /**
     * Creates a new rule factory instance.
     *
     * @param RuleRegistryInterface $ruleRegistry Registry of available rules
     */
    public function __construct(RuleRegistryInterface $ruleRegistry)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    /**
     * Creates a rule instance from a name and parameters.
     *
     * @param string $name The rule name
     * @param array<int|string, mixed> $parameters The rule parameters
     * @return RuleInterface The created rule instance
     * @throws InvalidArgumentException If the rule cannot be created
     */
    public function createRule(string $name, array $parameters = []): RuleInterface
    {
        // First check if we have a custom creator for this rule
        if (isset($this->creators[$name])) {
            $rule = $this->creators[$name]($parameters);

            if (! $rule instanceof RuleInterface) {
                throw new InvalidArgumentException(
                    "Custom creator for rule '$name' must return a RuleInterface instance"
                );
            }

            return $rule;
        }

        // Otherwise check if the rule exists in the registry
        $rule = $this->ruleRegistry->get($name);

        if (! $rule) {
            throw new InvalidArgumentException("Rule '$name' is not registered");
        }

        // Clone the rule to ensure we don't modify the registered instance
        $ruleClone = clone $rule;
        $ruleClone->setParameters($parameters);

        return $ruleClone;
    }

    /**
     * Registers a rule creator function.
     *
     * @param string $name The rule name
     * @param callable $creator A function that creates the rule
     * @return self The current instance for method chaining
     */
    public function registerRuleCreator(string $name, callable $creator): self
    {
        $this->creators[$name] = $creator;

        return $this;
    }

    /**
     * Checks if a rule creator exists.
     *
     * @param string $name The rule name
     * @return bool True if a creator exists, false otherwise
     */
    public function hasRuleCreator(string $name): bool
    {
        return isset($this->creators[$name]);
    }
}
