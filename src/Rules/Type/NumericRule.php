<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class NumericRule extends AbstractRule
{
    protected string $message = 'The :attribute must be a number.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_numeric($value);
    }
}
