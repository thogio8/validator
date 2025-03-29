<?php

namespace ValidatorPro\Contracts;

interface RuleInterface
{
    /**
     * @param mixed $value
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validate($value, array $parameters = [], array $data = []): bool;

    public function getMessage(): string;

    public function getName(): string;

    /**
     * @param array<int|string, mixed> $parameters
     * @return self
     */
    public function setParameters(array $parameters): self;

    /**
     * @return array<int|string, mixed>
     */
    public function getParameters(): array;

    public function setErrorMessage(?string $message): self;

    public function supportsType(string $type): bool;

    /**
     * @return array<string, mixed>
     */
    public function getDocumentation(): array;
}
