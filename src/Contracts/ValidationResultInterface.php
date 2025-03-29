<?php

namespace ValidatorPro\Contracts;

interface ValidationResultInterface
{
    public function passes(): bool;
    public function fails(): bool;
    public function getErrors(): array;
    public function getValidData(): array;
    public function getFirstErrors(): array;
    public function getError(string $field): ?string;
    public function hasError(string $field): bool;
    public function getFieldErrors(string $field): array;
    public function getErrorCount(): int;
    public function addError(string $field, string $message): self;
    public function getValidatedFields(): array;
}
