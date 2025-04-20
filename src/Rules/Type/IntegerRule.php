<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class IntegerRule extends AbstractRule
{
    protected string $message = 'The :attribute must be an integer.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_integer($value);
    }
}
