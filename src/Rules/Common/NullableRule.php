<?php

namespace ValidatorPro\Rules\Common;

use ValidatorPro\Rules\AbstractRule;

class NullableRule extends AbstractRule
{
    /**
     * @var string
     */
    protected string $message = 'Le champ :attribute est nullable';

    public function validate($value, array $parameters = [], array $data = []): bool
    {
        return true;
    }
}
