<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\CompiledRulesInterface;

class CompiledRules implements CompiledRulesInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $rules = [];

    /**
     * @var array<string, array<mixed>>
     */
    private array $compiledRules = [];

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getCompiledRules(): array
    {
        return $this->compiledRules;
    }

    public function addRule(string $field, $rule): self
    {
        $this->rules[$field] = $rule;

        return $this;
    }

    /**
     * @param string $field
     * @param array<string, mixed> $rules
     * @return self
     */
    public function addField(string $field, array $rules): self
    {
        $this->compiledRules[$field] = $rules;

        return $this;
    }

    public function hasRule(string $field): bool
    {
        return isset($this->compiledRules[$field]);
    }

    public function getRule(string $field): array
    {
        return $this->compiledRules[$field] ?? [];
    }
}
