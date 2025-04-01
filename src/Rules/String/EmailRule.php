<?php

namespace ValidatorPro\Rules\String;

use ValidatorPro\Rules\AbstractRule;

class EmailRule extends AbstractRule
{
    /**
     * @var string
     */
    protected string $message = 'Le champ :attribute doit être une adresse email valide';

    /**
     * Validate if the value is a valid email address
     *
     * @param mixed $value
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        // Vérifie que la valeur est une chaîne
        if (! is_string($value)) {
            return false;
        }

        // Utilise le filtre de validation d'email de PHP
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
