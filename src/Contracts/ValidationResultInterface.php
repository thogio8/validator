<?php

namespace ValidatorPro\Contracts;

interface ValidationResultInterface
{
    public function passes(): bool;

    public function fails(): bool;

    /**
     * @return array<string, array<string>>
     */
    public function getErrors(): array;

    /**
     * @return array<string, mixed>
     */
    public function getValidData(): array;

    /**
     * @return array<string, string>
     */
    public function getFirstErrors(): array;

    public function getError(string $field): ?string;

    public function hasError(string $field): bool;

    /**
     * @return array<string>
     */
    public function getFieldErrors(string $field): array;

    public function getErrorCount(): int;

    public function addError(string $field, string $message): self;

    /**
     * @return array<string>
     */
    public function getValidatedFields(): array;
}
