<?php

namespace ValidatorPro\Rules\Type;

use ValidatorPro\Rules\AbstractRule;

class ObjectRule extends AbstractRule
{
    protected string $message = 'The :attribute must be an object.';

    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return is_object($value);
    }
}
