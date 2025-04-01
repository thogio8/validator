<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for validation contexts.
 *
 * This interface defines the contract for validation context objects
 * that provide named sets of rules, messages, and attributes for
 * reusable validation scenarios.
 */
interface ContextInterface
{
    /**
     * Gets the name of the context.
     *
     * @return string The context name
     */
    public function getName(): string;

    /**
     * Gets the validation rules defined in this context.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getRules(): array;

    /**
     * Gets the custom error messages defined in this context.
     *
     * @return array<string, string> The custom error messages
     */
    public function getMessages(): array;

    /**
     * Gets all attributes defined in this context.
     *
     * @return array<string, mixed> All attributes
     */
    public function getAttributes(): array;

    /**
     * Gets a specific attribute value.
     *
     * @param string $name The attribute name
     * @param mixed|null $default Default value if attribute doesn't exist
     * @return mixed The attribute value or default
     */
    public function getAttribute(string $name, mixed $default = null): mixed;

    /**
     * Creates a new context with the specified attribute.
     *
     * @param string $name The attribute name
     * @param mixed $value The attribute value
     * @return self A new context instance with the attribute
     */
    public function withAttribute(string $name, mixed $value): self;

    /**
     * Creates a new context with the specified rule.
     *
     * @param string $field The field name
     * @param string|array<mixed> $rules The rule(s) to add
     * @return self A new context instance with the rule
     */
    public function withRule(string $field, array|string $rules): self;

    /**
     * Creates a new context with the specified error message.
     *
     * @param string $rule The rule name
     * @param string $message The error message
     * @return self A new context instance with the message
     */
    public function withMessage(string $rule, string $message): self;
}
