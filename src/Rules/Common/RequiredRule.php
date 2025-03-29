<?php

namespace ValidatorPro\Rules\Common;

use ValidatorPro\Rules\AbstractRule;

class RequiredRule extends AbstractRule
{
    /**
     * @var string
     */
    protected string $message = 'Le champ :attribute est obligatoire';

    /**
     * Validate if the value is present and not empty
     *
     * @param mixed $value
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validate($value, array $parameters = [], array $data = []): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_array($value) && count($value) === 0) {
            return false;
        }

        return true;
    }
}
