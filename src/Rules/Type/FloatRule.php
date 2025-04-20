<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class FloatRule extends AbstractRule
{
    protected string $message = 'The :attribute must be a float.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_float($value);
    }
}
