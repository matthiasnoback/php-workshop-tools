<?php
declare(strict_types=1);

namespace Common\EventDispatcher;

use Assert\Assertion;

final class EventDispatcher
{
    private $subscribers = [];
    private $subscribedToAllEvents = [];

    public function registerSubscriber(string $eventName, callable $subscriber)
    {
        $this->subscribers[$eventName][] = $subscriber;
    }

    public function subscribeToAllEvents(callable $subscriber)
    {
        $this->subscribedToAllEvents[] = $subscriber;
    }

    public function dispatch($event)
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

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
