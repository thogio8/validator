<?php

namespace ValidatorPro\Contracts;

interface CompiledRulesInterface
{
    public function getRules(): array;
    public function getCompiledRules(): array;
    public function addRule(string $field, $rule): self;
    public function hasRule(string $field): bool;
    public function getRule(string $field): array;
}
