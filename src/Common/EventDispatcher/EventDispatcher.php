<?php
declare(strict_types=1);

namespace Common\EventDispatcher;

use Assert\Assertion;

final class EventDispatcher
{
    private $subscribers = [];
    private $subscribedToAllEvents = [];

    /**
     * Subscribe to a single type of event that is dispatched through this event dispatcher, based on its full class name.
     *
     * @param string $eventClassName Only subscribe to events of this class
     * @param callable $subscriber Any callable accepting instances of the corresponding event class as its first argument
     */
    public function registerSubscriber(string $eventClassName, callable $subscriber): void
    {
        $this->subscribers[$eventClassName][] = $subscriber;
    }

    /**
     * Subscribe to any event that is dispatched through this event dispatcher.
     *
     * @param callable $subscriber Any callable accepting untyped event objects as its first argument
     */
    public function subscribeToAllEvents(callable $subscriber): void
    {
        $this->subscribedToAllEvents[] = $subscriber;
    }

    /**
     * Dispatch a single event.
     *
     * @param object $event The event object
     */
    public function dispatch($event): void
    {
        Assertion::isObject($event, 'An event should be an object');

        $eventName = get_class($event);
        $eventSubscribers = array_merge(
            $this->subscribedToAllEvents,
            $this->subscribers[$eventName] ?? []
        );

        foreach ($eventSubscribers as $eventSubscriber) {
            $eventSubscriber($event);
        }
    }

    /**
     * Dispatch all the given events.
     *
     * @param object[]|array $events An array of event objects
     */
    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
