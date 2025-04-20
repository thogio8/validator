<?php

namespace ValidatorPro\Services;

/**
 * Service class providing common validation utilities.
 *
 * This class centralizes common functions used across the validation process
 * to eliminate code duplication.
 */
class ValidationService
{
    /**
     * Gets a value from data using dot notation.
     *
     * @param string $field The field name (can use dot notation)
     * @param array<string, mixed> $data The data to extract from
     * @return mixed The field value or null if not found
     */
    public static function getValueForField(string $field, array $data): mixed
    {
        if (str_contains($field, '.')) {
            $segments = explode('.', $field);
            $current = $data;

            foreach ($segments as $segment) {
                if (! isset($current[$segment])) {
                    return null;
                }
                $current = $current[$segment];
            }

            return $current;
        }

        return $data[$field] ?? null;
    }
}
