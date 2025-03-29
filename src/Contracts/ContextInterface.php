<?php

namespace ValidatorPro\Contracts;

interface ContextInterface
{
    public function getName(): string;
    public function getRules(): array;
    public function getMessages(): array;
    public function getAttributes(): array;
    public function getAttribute(string $name, $default = null);
    public function withAttribute(string $name, $value): self;
    public function withRule(string $field, $rules): self;
    public function withMessage(string $rule, string $message): self;
}
