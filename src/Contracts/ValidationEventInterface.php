<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for validation events.
 *
 * This interface defines the structure for events that occur
 * during the validation process.
 */
interface ValidationEventInterface
{
    /**
     * Gets the name of the event.
     *
     * @return string The event name
     */
    public function getName(): string;

    /**
     * Gets the data associated with this event.
     *
     * @return array<string, mixed> The event data
     */
    public function getData(): array;

    /**
     * Gets the validation result if available.
     *
     * @return ValidationResultInterface|null The validation result or null if not applicable
     */
    public function getResult(): ?ValidationResultInterface;

    /**
     * Sets the validation result.
     *
     * @param ValidationResultInterface $result The validation result
     * @return self The current instance for method chaining
     */
    public function setResult(ValidationResultInterface $result): self;
}
