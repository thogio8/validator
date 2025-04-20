<?php

namespace ValidatorPro\Contracts\Rules;

/**
 * Interface for rules that only work with specific data types.
 *
 * This interface defines methods for rules that are designed to
 * validate only certain types of data.
 */
interface TypeRestrictedRuleInterface
{
    /**
     * Gets the list of data types this rule supports.
     *
     * @return array<string> List of supported data types (string, array, numeric, etc.)
     */
    public function getSupportedTypes(): array;

    /**
     * Checks if this rule supports a specific data type.
     *
     * @param string $type The data type to check
     * @return bool True if this rule supports the type, false otherwise
     */
    public function supportsType(string $type): bool;
}
