<?php

namespace ValidatorPro\Contracts;

interface ValidatorInterface
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|string $rules
     * @param array<string, string> $messages
     * @return ValidationResultInterface
     */
    public function validate(array $data, array|string $rules, array $messages = []): ValidationResultInterface;

    /**
     * @param string $name
     * @param callable|RuleInterface $rule
     * @return self
     */
    public function addRule(string $name, $rule): self;

    public function setContext(ContextInterface $context): self;

    /**
     * @param array<string, mixed>|string $rules
     * @return CompiledRulesInterface
     */
    public function compile(array|string $rules): CompiledRulesInterface;

    /**
     * @param string $name
     * @param callable|string $extension
     * @return self
     */
    public function extend(string $name, callable|string $extension): self;

    public function getRule(string $name): ?RuleInterface;

    public function hasRule(string $name): bool;

    /**
     * @return array<string, RuleInterface>
     */
    public function getAvailableRules(): array;
}
