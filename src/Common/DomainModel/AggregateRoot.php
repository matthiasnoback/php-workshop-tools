<?php
declare(strict_types = 1);

namespace Common\DomainModel;

use Assert\Assertion;

/**
 * Aggregate roots can use this trait
 */
trait AggregateRoot
{
    private $events = [];

    /**
     * Keep track of
     *
     * @param object $event
     */
    protected function recordThat($event)
    {
        Assertion::isObject($event, 'An event should be an object');

        $this->events[] = $event;
    }

    /**
     * @return object[]
     */
    final public function recordedEvents()
    {
        return $this->events;
    }

    public function clearEvents() : void
    {
        $this->events = [];
    }
}
