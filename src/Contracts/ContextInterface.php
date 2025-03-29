<?php

namespace ValidatorPro\Contracts;

interface ContextInterface
{
    public function getName(): string;

    /**
     * @return array<string, mixed>
     */
    public function getRules(): array;

    /**
     * @return array<string, string>
     */
    public function getMessages(): array;

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function withAttribute(string $name, $value): self;

    /**
     * @param string $field
     * @param string|array<mixed> $rules
     * @return self
     */
    public function withRule(string $field, $rules): self;

    public function withMessage(string $rule, string $message): self;
}
