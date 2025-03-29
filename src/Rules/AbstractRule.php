<?php

namespace ValidatorPro\Rules;

use ValidatorPro\Contracts\RuleInterface;

abstract class AbstractRule implements RuleInterface
{
    /**
     * @var array<int, mixed>
     */
    protected array $parameters = [];
    
    /**
     * @var string
     */
    protected string $message = 'Validation failed for :attribute';
    
    /**
     * Create a new rule instance
     *
     * @param array<int, mixed> $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
    
    /**
     * Get the validation error message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
    /**
     * Get the rule name
     * 
     * @return string
     */
    public function getName(): string
    {
        // Extract class name without namespace
        $className = get_class($this);
        $parts = explode('\\', $className);
        $simpleName = end($parts);
        
        // Remove "Rule" suffix and convert to snake_case
        return $this->camelToSnake(str_replace('Rule', '', $simpleName));
    }
    
    /**
     * Get parameters for this rule
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    /**
     * Set parameters for this rule
     *
     * @param array $parameters
     * @return self
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }
    
    /**
     * Set custom error message
     *
     * @param string|null $message
     * @return self
     */
    public function setErrorMessage(?string $message): self
    {
        if ($message !== null) {
            $this->message = $message;
        }
        return $this;
    }
    
    /**
     * Check if this rule supports a specific data type
     *
     * @param string $type
     * @return bool
     */
    public function supportsType(string $type): bool
    {
        return true; // By default, rules support all types, override in specific rules
    }
    
    /**
     * Get documentation for this rule
     *
     * @return array
     */
    public function getDocumentation(): array
    {
        return [
            'name' => $this->getName(),
            'description' => 'Documentation for ' . $this->getName() . ' rule',
            'parameters' => [],
            'examples' => []
        ];
    }
    
    /**
     * Convert camelCase to snake_case
     *
     * @param string $input
     * @return string
     */
    protected function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
