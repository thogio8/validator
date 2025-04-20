<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class BooleanRule extends AbstractRule
{
    protected string $message = 'The :attribute must be a boolean.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_bool($value);
    }
}
