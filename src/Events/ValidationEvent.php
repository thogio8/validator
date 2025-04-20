<?php

namespace ValidatorPro\Events;

use ValidatorPro\Contracts\ValidationEventInterface;
use ValidatorPro\Contracts\ValidationResultInterface;

/**
 * Basic implementation of a validation event.
 *
 * This class represents events that occur during the validation process.
 */
class ValidationEvent implements ValidationEventInterface
{
    /**
     * The name of the event.
     *
     * @var string
     */
    private string $name;

    /**
     * Data associated with this event.
     *
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * The validation result, if applicable.
     *
     * @var ValidationResultInterface|null
     */
    private ?ValidationResultInterface $result;

    /**
     * Creates a new validation event.
     *
     * @param string $name The event name
     * @param array<string, mixed> $data Data associated with the event
     * @param ValidationResultInterface|null $result The validation result if available
     */
    public function __construct(
        string $name,
        array $data = [],
        ?ValidationResultInterface $result = null
    ) {
        $this->name = $name;
        $this->data = $data;
        $this->result = $result;
    }

    /**
     * Gets the name of the event.
     *
     * @return string The event name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the data associated with this event.
     *
     * @return array<string, mixed> The event data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Gets the validation result if available.
     *
     * @return ValidationResultInterface|null The validation result or null if not applicable
     */
    public function getResult(): ?ValidationResultInterface
    {
        return $this->result;
    }

    /**
     * Sets the validation result.
     *
     * @param ValidationResultInterface $result The validation result
     * @return self The current instance for method chaining
     */
    public function setResult(ValidationResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}
