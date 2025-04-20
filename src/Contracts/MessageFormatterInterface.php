<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for formatting validation error messages.
 *
 * This interface defines methods for formatting validation error
 * messages with field names, parameters, and other context.
 */
interface MessageFormatterInterface
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
    public function format(string $message, string $field, array $parameters = [], array $context = []): string;
}
