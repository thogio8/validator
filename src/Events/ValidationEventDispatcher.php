<?php

namespace ValidatorPro\Events;

use ValidatorPro\Contracts\ValidationEventDispatcherInterface;
use ValidatorPro\Contracts\ValidationEventInterface;

/**
 * Implementation of the validation event dispatcher.
 *
 * This class manages event listeners and dispatches events to them.
 */
class ValidationEventDispatcher implements ValidationEventDispatcherInterface
{
    /**
     * Array of registered event listeners.
     *
     * @var array<string, array<int, array<callable>>>
     */
    private array $listeners = [];

    /**
     * Adds a listener for a specific event.
     *
     * @param string $eventName The name of the event to listen for
     * @param callable $listener The listener function
     * @param int $priority The priority of this listener (higher executes earlier)
     * @return self The current instance for method chaining
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): self
    {
        if (! isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        if (! isset($this->listeners[$eventName][$priority])) {
            $this->listeners[$eventName][$priority] = [];
        }

        $this->listeners[$eventName][$priority][] = $listener;

        return $this;
    }

    /**
     * Removes a listener from an event.
     *
     * @param string $eventName The name of the event
     * @param callable $listener The listener to remove
     * @return bool True if the listener was found and removed, false otherwise
     */
    public function removeListener(string $eventName, callable $listener): bool
    {
        if (! isset($this->listeners[$eventName])) {
            return false;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $index => $registeredListener) {
                if ($registeredListener === $listener) {
                    unset($this->listeners[$eventName][$priority][$index]);

                    // Clean up empty arrays
                    if (empty($this->listeners[$eventName][$priority])) {
                        unset($this->listeners[$eventName][$priority]);
                    }

                    if (empty($this->listeners[$eventName])) {
                        unset($this->listeners[$eventName]);
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param ValidationEventInterface $event The event to dispatch
     * @return ValidationEventInterface The possibly modified event
     */
    public function dispatch(ValidationEventInterface $event): ValidationEventInterface
    {
        $eventName = $event->getName();

        if (! isset($this->listeners[$eventName])) {
            return $event;
        }

        // Sort by priority (higher numbers first)
        $priorities = array_keys($this->listeners[$eventName]);
        rsort($priorities);

        foreach ($priorities as $priority) {
            foreach ($this->listeners[$eventName][$priority] as $listener) {
                $listener($event);
            }
        }

        return $event;
    }
}
