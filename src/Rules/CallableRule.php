<?php

namespace ValidatorPro\Rules;

class CallableRule extends AbstractRule
{
    /**
     * @var callable
     */
    private $callable;

    private string $ruleName;

    public function __construct(callable $callable, string $name)
    {
        parent::__construct();
        $this->callable = $callable;
        $this->ruleName = $name;
        $this->message = 'Le champ :attribute a échoué à la validation.';
    }

    public function validate($value, array $parameters = [], array $data = []): bool
    {
        return call_user_func($this->callable, $value, $parameters, $data);
    }

    public function getName(): string
    {
        return $this->ruleName;
    }
}
