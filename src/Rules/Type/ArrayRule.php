<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class ArrayRule extends AbstractRule
{
    protected string $message = 'The :attribute must be an array.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_array($value);
    }
}
