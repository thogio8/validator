<?php

namespace ValidatorPro\Contracts;

interface RuleInterface
{
    public function validate($value, array $parameters = [], array $data = []): bool;
    public function getMessage(): string;
    public function getName(): string;
    public function setParameters(array $parameters): self;
    public function getParameters(): array;
    public function setErrorMessage(?string $message): self;
    public function supportsType(string $type): bool;
    public function getDocumentation(): array;
}
