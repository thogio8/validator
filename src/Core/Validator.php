<?php

namespace ValidatorPro\Core;

use InvalidArgumentException;
use ValidatorPro\Contracts\CompiledRulesInterface;
use ValidatorPro\Contracts\ContextInterface;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;
use ValidatorPro\Contracts\ValidationResultInterface;
use ValidatorPro\Contracts\ValidationStrategyInterface;
use ValidatorPro\Contracts\ValidatorInterface;
use ValidatorPro\Validation\StopOnFirstErrorStrategy;

/**
 * Main validator class for data validation.
 *
 * This class is the central component of the ValidatorPro library,
 * responsible for validating data against defined rules and providing
 * structured results. It's designed to be flexible, extensible, and
 * compliant with SOLID principles.
 */
class Validator implements ValidatorInterface
{
    /**
     * Registry of available validation rules.
     *
     * @var RuleRegistryInterface
     */
    private RuleRegistryInterface $ruleRegistry;

    /**
     * Current validation context.
     *
     * @var ContextInterface|null
     */
    private ?ContextInterface $context = null;

    /**
     * Custom validator extensions.
     *
     * @var array<string, callable|string>
     */
    private array $extensions = [];

    /**
     * Current validation strategy.
     *
     * @var ValidationStrategyInterface
     */
    private ValidationStrategyInterface $strategy;

    /**
     * Creates a new validator instance.
     *
     * @param RuleRegistryInterface $ruleRegistry Registry of available validation rules
     * @param ValidationStrategyInterface|null $strategy Optional custom validation strategy
     */
    public function __construct(
        RuleRegistryInterface $ruleRegistry,
        ?ValidationStrategyInterface $strategy = null
    ) {
        $this->ruleRegistry = $ruleRegistry;
        $this->strategy = $strategy ?? new StopOnFirstErrorStrategy($ruleRegistry);
    }

    /**
     * Sets the validation strategy.
     *
     * @param ValidationStrategyInterface $strategy The validation strategy to use
     * @return self The current instance for method chaining
     */
    public function setStrategy(ValidationStrategyInterface $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Validates data against rules.
     *
     * @param array<string, mixed> $data The data to validate
     * @param array<string, mixed>|string $rules The validation rules
     * @param array<string, string> $messages Custom error messages
     * @return ValidationResultInterface The validation result
     */
    public function validate(array $data, array|string $rules, array $messages = []): ValidationResultInterface
    {
        $compiledRules = $this->compile($rules);

        $contextMessages = [];
        if ($this->context !== null) {
            $contextMessages = $this->context->getMessages();
        }

        $allMessages = array_merge($contextMessages, $messages);

        // Set extensions on the strategy
        if (method_exists($this->strategy, 'setExtensions')) {
            $this->strategy->setExtensions($this->extensions);
        }

        // Delegate validation to the strategy
        return $this->strategy->validate($data, $compiledRules, $allMessages);
    }

    /**
     * Adds a validation rule to the validator.
     *
     * @param string $name The rule name
     * @param callable|RuleInterface $rule The rule implementation
     * @return self The current instance for method chaining
     * @throws InvalidArgumentException If the rule is a callable but not a RuleInterface
     */
    public function addRule(string $name, callable|RuleInterface $rule): self
    {
        if (is_callable($rule) && ! ($rule instanceof RuleInterface)) {
            throw new InvalidArgumentException("Callable rules are not supported yet. Please provide a RuleInterface instance.");
        }

        if ($rule instanceof RuleInterface) {
            $this->ruleRegistry->register($name, $rule);
        }

        return $this;
    }

    /**
     * Sets the validation context.
     *
     * @param ContextInterface $context The validation context
     * @return self The current instance for method chaining
     */
    public function setContext(ContextInterface $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Compiles rules into a normalized format.
     *
     * @param array<string, mixed>|string $rules The rules to compile
     * @return CompiledRulesInterface The compiled rules
     */
    public function compile(array|string $rules): CompiledRulesInterface
    {
        if (is_string($rules)) {
            return new CompiledRules(['_default' => $rules]);
        }

        if ($this->context !== null) {
            $contextRules = $this->context->getRules();
            if (! empty($contextRules)) {
                $rules = array_merge($contextRules, $rules);
            }
        }

        return new CompiledRules($rules);
    }

    /**
     * Extends the validator with a custom rule or extension.
     *
     * @param string $name The extension name
     * @param callable|string $extension The extension implementation
     * @return self The current instance for method chaining
     */
    public function extend(string $name, callable|string $extension): self
    {
        $this->extensions[$name] = $extension;

        return $this;
    }

    /**
     * Gets a rule by name.
     *
     * @param string $name The rule name
     * @return RuleInterface|null The rule or null if not found
     */
    public function getRule(string $name): ?RuleInterface
    {
        return $this->ruleRegistry->get($name);
    }

    /**
     * Checks if a rule exists.
     *
     * @param string $name The rule name
     * @return bool True if the rule exists, false otherwise
     */
    public function hasRule(string $name): bool
    {
        return $this->ruleRegistry->has($name);
    }

    /**
     * Gets all available rules.
     *
     * @return array<string, RuleInterface> All available rules indexed by name
     */
    public function getAvailableRules(): array
    {
        return $this->ruleRegistry->all();
    }
}
