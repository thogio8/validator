<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class StringRule extends AbstractRule
{
    protected string $message = 'The :attribute must be a string.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_string($value);
    }
}
