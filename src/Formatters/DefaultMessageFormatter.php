<?php

namespace ValidatorPro\Formatters;

use ValidatorPro\Contracts\MessageFormatterInterface;

/**
 * Default implementation of message formatter.
 *
 * This class provides the standard formatting logic for validation error messages.
 */
class DefaultMessageFormatter implements MessageFormatterInterface
{
    /**
     * Formats an error message.
     *
     * @param string $message The message template
     * @param string $field The field name
     * @param array<int|string, mixed> $parameters The parameters to insert
     * @param array<string, mixed> $context Additional context for formatting
     * @return string The formatted message
     */
    public function format(string $message, string $field, array $parameters = [], array $context = []): string
    {
        // Replace field placeholder
        $message = str_replace(':attribute', $field, $message);

        // Replace numeric parameters (param0, param1, etc.)
        foreach ($parameters as $key => $value) {
            if (is_string($key)) {
                // Named parameters
                $message = str_replace(":$key", (string)$value, $message);
            } else {
                // Indexed parameters
                $message = str_replace(":param$key", (string)$value, $message);
            }
        }

        // Handle additional context placeholders
        foreach ($context as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $message = str_replace(":$key", (string)$value, $message);
            }
        }

        return $message;
    }
}
