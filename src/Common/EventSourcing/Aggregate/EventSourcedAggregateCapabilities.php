<?php
declare(strict_types=1);

namespace Common\EventSourcing\Aggregate;

use Assert\Assertion;

/**
 * Using this trait in an event-sourced aggregate will make it fully compliant to the contract defined by the
 * `EventSourced` interface.
 */
trait EventSourcedAggregateCapabilities
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var object[]
     */
    private $recordedEvents = [];

    /**
     * Enforce construction through either a named constructor or the `reconstitute()` method.
     */
    private function __construct()
    {
    }

    public function popRecordedEvents()
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }

    public function id(): string
    {
        $id = is_string($this->id) ? $this->id : (string)$this->id;
        Assertion::notEmpty($id, 'Aggregate ID is empty');

        return $id;
    }

    public static function reconstitute(\Iterator $events)
    {
        $instance = new static();

        foreach ($events as $event) {
            $instance->apply($event);
        }

        return $instance;
    }

    private function recordThat($event)
    {
        Assertion::isObject($event, 'A domain event should be an object');

        $this->recordedEvents[] = $event;
        $this->apply($event);
    }

    private function apply($event)
    {
        Assertion::isObject($event, 'A domain event should be an object');

        $parts = explode('\\', get_class($event));
        $eventName = end($parts);
        $name = 'when' . $eventName;

        $applyFunction = [$this, $name];
        Assertion::true(is_callable($applyFunction), sprintf(
            'You first need to define the following method in class %s: private function %s(%s $event) { }',
            get_class($this),
            $name,
            get_class($event)
        ));
        call_user_func($applyFunction, $event);
    }
}
