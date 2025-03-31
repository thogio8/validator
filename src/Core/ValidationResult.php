<?php

namespace ValidatorPro\Core;

use ValidatorPro\Contracts\ValidationResultInterface;

class ValidationResult implements ValidationResultInterface
{
    /**
     * @var array<string, array<string>>
     */
    private array $errors = [];

    /**
     * @var array<string, mixed>
     */
    private array $validData = [];

    /**
     * @var array<string>
     */
    private array $validatedFields = [];

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidData(): array
    {
        return $this->validData;
    }

    public function getFirstErrors(): array
    {
        $firstErrors = [];
        foreach ($this->errors as $field => $messages) {
            if (! empty($messages)) {
                $firstErrors[$field] = $messages[0];
            }
        }

        return $firstErrors;
    }

    public function getError(string $field): ?string
    {
        return $this->getFieldErrors($field)[0] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && ! empty($this->errors[$field]);
    }

    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    public function getErrorCount(): int
    {
        $count = 0;
        foreach ($this->errors as $fieldErrors) {
            $count += count($fieldErrors);
        }

        return $count;
    }

    public function addError(string $field, string $message): self
    {
        if (! isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function addValidData(string $field, mixed $value): self
    {
        $this->validData[$field] = $value;

        return $this;
    }

    public function getValidatedFields(): array
    {
        return $this->validatedFields;
    }

    public function addValidatedField(string $field): self
    {
        if (! in_array($field, $this->validatedFields)) {
            $this->validatedFields[] = $field;
        }

        return $this;
    }
}
