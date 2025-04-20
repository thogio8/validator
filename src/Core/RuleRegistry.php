<?php

namespace ValidatorPro\Core;

use InvalidArgumentException;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;

/**
 * Rule Registry for managing validation rules.
 *
 * This class provides a centralized storage and management system for validation rules,
 * allowing registration, retrieval, and checking of available rules in the validator.
 * It implements the RuleRegistryInterface and follows the Single Responsibility Principle.
 */
class RuleRegistry implements RuleRegistryInterface
{
    /**
     * Array of registered rules.
     *
     * @var array<string, RuleInterface>
     */
    private array $rules;

    /**
     * Creates a new rule registry instance.
     *
     * @param array<string, RuleInterface> $rules Initial rules to register
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Registers a rule in the registry.
     *
     * @param string $name The name of the rule
     * @param RuleInterface $rule The rule instance
     * @return self The current instance for method chaining
     * @throws InvalidArgumentException If the rule name is empty
     */
    public function register(string $name, RuleInterface $rule): self
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Rule name cannot be empty');
        }

        $this->rules[$name] = $rule;

        return $this;
    }

    /**
     * Retrieves a rule by its name.
     *
     * @param string $name The name of the rule to retrieve
     * @return RuleInterface|null The rule instance or null if not found
     */
    public function get(string $name): ?RuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    /**
     * Checks if a rule exists in the registry.
     *
     * @param string $name The name of the rule to check
     * @return bool True if the rule exists, false otherwise
     */
    public function has(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    /**
     * Retrieves all available rules in the registry.
     *
     * @return array<string, RuleInterface> All rules indexed by their name
     */
    public function all(): array
    {
        return $this->rules;
    }
}
