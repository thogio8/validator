<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\ContextInterface;

/**
 * ValidationContext class implementing the ContextInterface.
 *
 * This class provides a reusable container for validation rules, custom error messages,
 * and additional context attributes. It follows an immutable pattern where
 * methods that change the state return a new instance rather than modifying
 * the current one.
 */
class ValidationContext implements ContextInterface
{
    /**
     * The name of the validation context.
     *
     * @var string
     */
    private string $name;

    /**
     * The validation rules associated with this context.
     *
     * @var array<string, mixed>
     */
    private array $rules;

    /**
     * Custom error messages for validation failures.
     *
     * @var array<string, string>
     */
    private array $messages;

    /**
     * Additional attributes associated with this context.
     *
     * @var array<string, mixed>
     */
    private array $attributes;

    /**
     * Creates a new validation context with the given name.
     *
     * @param string $name The name of the context
     * @param array<string, mixed> $rules Initial validation rules (optional)
     * @param array<string, string> $messages Initial custom error messages (optional)
     * @param array<string, mixed> $attributes Initial context attributes (optional)
     */
    public function __construct(
        string $name,
        array $rules = [],
        array $messages = [],
        array $attributes = []
    ) {
        $this->name = $name;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->attributes = $attributes;
    }

    /**
     * Gets the name of the context.
     *
     * @return string The context name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the validation rules defined in this context.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Gets the custom error messages defined in this context.
     *
     * @return array<string, string> The custom error messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Gets all attributes defined in this context.
     *
     * @return array<string, mixed> All attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Gets a specific attribute value.
     *
     * @param string $name The attribute name
     * @param mixed $default Default value if attribute doesn't exist
     * @return mixed The attribute value or default
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Creates a new context with the specified attribute.
     *
     * @param string $name The attribute name
     * @param mixed $value The attribute value
     * @return self A new context instance with the attribute
     */
    public function withAttribute(string $name, mixed $value): self
    {
        $attributes = $this->attributes;
        $attributes[$name] = $value;

        return new self($this->name, $this->rules, $this->messages, $attributes);
    }

    /**
     * Creates a new context with the specified rule.
     *
     * @param string $field The field name
     * @param string|array<mixed> $rules The rule(s) to add
     * @return self A new context instance with the rule
     */
    public function withRule(string $field, array|string $rules): self
    {
        $newRules = $this->rules;
        $newRules[$field] = $rules;

        return new self($this->name, $newRules, $this->messages, $this->attributes);
    }

    /**
     * Creates a new context with the specified error message.
     *
     * @param string $rule The rule name
     * @param string $message The error message
     * @return self A new context instance with the message
     */
    public function withMessage(string $rule, string $message): self
    {
        $newMessages = $this->messages;
        $newMessages[$rule] = $message;

        return new self($this->name, $this->rules, $newMessages, $this->attributes);
    }
}
