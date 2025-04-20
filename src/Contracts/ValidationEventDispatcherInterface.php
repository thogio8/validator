<?php

namespace ValidatorPro\Contracts;

/**
 * Interface for validation event dispatchers.
 *
 * This interface defines methods for registering listeners and
 * dispatching events during the validation process.
 */
interface ValidationEventDispatcherInterface
{
    /**
     * Adds a listener for a specific event.
     *
     * @param string $eventName The name of the event to listen for
     * @param callable $listener The listener function
     * @param int $priority The priority of this listener (higher executes earlier)
     * @return self The current instance for method chaining
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): self;

    /**
     * Removes a listener from an event.
     *
     * @param string $eventName The name of the event
     * @param callable $listener The listener to remove
     * @return bool True if the listener was found and removed, false otherwise
     */
    public function removeListener(string $eventName, callable $listener): bool;

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param ValidationEventInterface $event The event to dispatch
     * @return ValidationEventInterface The possibly modified event
     */
    public function dispatch(ValidationEventInterface $event): ValidationEventInterface;
}
