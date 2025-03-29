<?php

namespace ValidatorPro\Contracts;

interface ValidatorInterface
{
    public function validate(array $data, array|string $rules, array $messages = []): ValidationResultInterface;
    public function addRule(string $name, $rule): self;
    public function setContext(ContextInterface $context): self;
    public function compile(array|string $rules): CompiledRulesInterface;
    public function extend(string $name, callable|string $extension): self;
    public function getRule(string $name): ?RuleInterface;
    public function hasRule(string $name): bool;
    public function getAvailableRules(): array;
}
