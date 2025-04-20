<?php

namespace ValidatorPro\Validation;

use InvalidArgumentException;
use ValidatorPro\Contracts\CompiledRulesInterface;
use ValidatorPro\Contracts\MessageFormatterInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;
use ValidatorPro\Contracts\ValidationResultInterface;
use ValidatorPro\Contracts\ValidationStrategyInterface;
use ValidatorPro\Core\ValidationResult;
use ValidatorPro\Formatters\DefaultMessageFormatter;
use ValidatorPro\Services\ValidationService;

/**
 * Abstract base class for validation strategies.
 *
 * This class provides common functionality for all validation strategies,
 * reducing code duplication while enabling specific behavior in subclasses.
 */
abstract class AbstractValidationStrategy implements ValidationStrategyInterface
{
    /**
     * Registry of available validation rules.
     *
     * @var RuleRegistryInterface
     */
    protected RuleRegistryInterface $ruleRegistry;

    /**
     * Message formatter for error messages.
     *
     * @var MessageFormatterInterface
     */
    protected MessageFormatterInterface $messageFormatter;

    /**
     * Custom validator extensions.
     *
     * @var array<string, callable|string>
     */
    protected array $extensions = [];

    /**
     * Creates a new validation strategy instance.
     *
     * @param RuleRegistryInterface $ruleRegistry Registry of available rules
     * @param MessageFormatterInterface|null $messageFormatter Optional custom message formatter
     */
    public function __construct(
        RuleRegistryInterface $ruleRegistry,
        ?MessageFormatterInterface $messageFormatter = null
    ) {
        $this->ruleRegistry = $ruleRegistry;
        $this->messageFormatter = $messageFormatter ?? new DefaultMessageFormatter();
    }

    /**
     * Sets custom validator extensions.
     *
     * @param array<string, callable|string> $extensions Array of custom extensions
     * @return self The current instance for method chaining
     */
    public function setExtensions(array $extensions): self
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * Validates data against rules using this strategy.
     *
     * @param array<string, mixed> $data The data to validate
     * @param CompiledRulesInterface $rules The compiled validation rules
     * @param array<string, string> $messages Custom error messages
     * @return ValidationResultInterface The validation result
     */
    public function validate(
        array $data,
        CompiledRulesInterface $rules,
        array $messages = []
    ): ValidationResultInterface {
        $validationResult = new ValidationResult();
        $validData = [];

        foreach ($rules->getCompiledRules() as $field => $fieldRules) {
            if ($field === '_default' && count($rules->getCompiledRules()) > 1) {
                continue;
            }

            $actualField = $field === '_default' ? array_key_first($data) : $field;
            $value = $this->getValueForField($actualField, $data);

            $errors = $this->validateField($actualField, $value, $fieldRules, $data, $messages);

            if (! empty($errors)) {
                foreach ($errors as $error) {
                    $validationResult->addError($actualField, $error);
                }
            } else {
                if (array_key_exists($actualField, $data) || $this->getValueForField($actualField, $data) !== null) {
                    $validData[$actualField] = $value;
                }
            }
        }

        foreach ($validData as $field => $value) {
            if (! $validationResult->hasError($field)) {
                $validationResult->addValidData($field, $value);
            }
        }

        return $validationResult;
    }

    /**
     * Validates a single field against its rules.
     * This method should be implemented by subclasses with specific validation behavior.
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array<array<string, mixed>> $rules The rules for this field
     * @param array<string, mixed> $data All data being validated
     * @param array<string, string> $messages Custom error messages
     * @return array<string> Array of error messages for this field
     */
    abstract protected function validateField(
        string $field,
        mixed $value,
        array $rules,
        array $data,
        array $messages = []
    ): array;

    /**
     * Validates a value against a specific rule.
     *
     * @param string $field The field name
     * @param mixed $value The value to validate
     * @param array<string, mixed> $rule The rule configuration
     * @param array<string, mixed> $data All data being validated
     * @param array<string, string> $messages Custom error messages
     * @return string|null Error message if validation fails, null otherwise
     * @throws InvalidArgumentException If the rule is not registered
     */
    protected function validateRule(
        string $field,
        mixed $value,
        array $rule,
        array $data,
        array $messages = []
    ): ?string {
        $ruleName = $rule['name'];
        $parameters = $rule['parameters'] ?? [];

        if (isset($this->extensions[$ruleName])) {
            $extension = $this->extensions[$ruleName];

            if (is_callable($extension)) {
                $valid = $extension($value, $parameters, $data);
                if (! $valid) {
                    return $this->getErrorMessage($field, $ruleName, $parameters, $messages);
                }

                return null;
            }
        }

        $ruleInstance = $this->ruleRegistry->get($ruleName);

        if (! $ruleInstance) {
            throw new InvalidArgumentException("Rule '$ruleName' is not registered.");
        }

        $ruleInstance->setParameters($parameters);

        $valid = $ruleInstance->validate($value, $parameters, $data);

        if (! $valid) {
            return $this->getErrorMessage($field, $ruleName, $parameters, $messages, $ruleInstance->getMessage());
        }

        return null;
    }

    /**
     * Gets the appropriate error message for a failed validation.
     *
     * @param string $field The field name
     * @param string $ruleName The rule name
     * @param array<mixed> $parameters The rule parameters
     * @param array<string, string> $customMessages Custom error messages
     * @param string|null $defaultMessage Default error message
     * @return string The formatted error message
     */
    protected function getErrorMessage(
        string $field,
        string $ruleName,
        array $parameters,
        array $customMessages,
        ?string $defaultMessage = null
    ): string {
        $messageKey = "$field.$ruleName";

        $message = $customMessages[$messageKey]
            ?? $customMessages[$ruleName]
            ?? $defaultMessage
            ?? "The $field field validation failed.";

        return $this->messageFormatter->format($message, $field, $parameters);
    }

    /**
     * Gets a value from data using dot notation.
     *
     * @param string $field The field name (can use dot notation)
     * @param array<string, mixed> $data The data to extract from
     * @return mixed The field value or null if not found
     */
    protected function getValueForField(string $field, array $data): mixed
    {
        return ValidationService::getValueForField($field, $data);
    }
}
