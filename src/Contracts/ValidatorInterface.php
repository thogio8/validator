<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for validator objects.
 *
 * This interface defines the contract for validators that can validate data
 * against rules, manage validation rules, and work with validation contexts.
 */
interface ValidatorInterface
{
    /**
     * Validates data against rules.
     *
     * @param array<string, mixed> $data The data to validate
     * @param array<string, mixed>|string $rules The validation rules
     * @param array<string, string> $messages Custom error messages
     * @return ValidationResultInterface The validation result
     */
    public function validate(array $data, array|string $rules, array $messages = []): ValidationResultInterface;

    /**
     * Adds a validation rule to the validator.
     *
     * @param string $name The rule name
     * @param callable|RuleInterface $rule The rule implementation
     * @return self The current instance for method chaining
     */
    public function addRule(string $name, $rule): self;

    /**
     * Sets the validation context.
     *
     * @param ContextInterface $context The validation context
     * @return self The current instance for method chaining
     */
    public function setContext(ContextInterface $context): self;

    /**
     * Compiles rules into a normalized format.
     *
     * @param array<string, mixed>|string $rules The rules to compile
     * @return CompiledRulesInterface The compiled rules
     */
    public function compile(array|string $rules): CompiledRulesInterface;

    /**
     * Extends the validator with a custom rule or extension.
     *
     * @param string $name The extension name
     * @param callable|string $extension The extension implementation
     * @return self The current instance for method chaining
     */
    public function extend(string $name, callable|string $extension): self;

    /**
     * Gets a rule by name.
     *
     * @param string $name The rule name
     * @return RuleInterface|null The rule or null if not found
     */
    public function getRule(string $name): ?RuleInterface;

    /**
     * Checks if a rule exists.
     *
     * @param string $name The rule name
     * @return bool True if the rule exists, false otherwise
     */
    public function hasRule(string $name): bool;

    /**
     * Gets all available rules.
     *
     * @return array<string, RuleInterface> All available rules indexed by name
     */
    public function getAvailableRules(): array;
}
