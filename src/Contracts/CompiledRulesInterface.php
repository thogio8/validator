<?php

namespace ValidatorPro\Contracts;

interface CompiledRulesInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getRules(): array;

    /**
     * @return array<string, array<mixed>>
     */
    public function getCompiledRules(): array;

    /**
     * @param string $field
     * @param mixed $rule
     * @return self
     */
    public function addRule(string $field, $rule): self;

    public function hasRule(string $field): bool;

    /**
     * @param string $field
     * @return array<mixed>
     */
    public function getRule(string $field): array;
}
