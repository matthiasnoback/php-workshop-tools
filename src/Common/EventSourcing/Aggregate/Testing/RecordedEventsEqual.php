<?php
declare(strict_types = 1);

namespace Common\EventSourcing\Aggregate\Testing;

use Common\EventSourcing\Aggregate\EventSourcedAggregate;

class RecordedEventsEqual extends \PHPUnit_Framework_Constraint
{
    /**
     * @var EventSourcedAggregate
     */
    private $aggregate;

    public function __construct(EventSourcedAggregate $aggregate)
    {
        parent::__construct();

        $this->aggregate = $aggregate;
    }

    protected function matches($expectedEvents)
    {
        $actualEvents = $this->aggregate->popRecordedEvents();

        if (count($expectedEvents) !== count($actualEvents)) {
            return false;
        }

        foreach ($expectedEvents as $key => $expectedEvent) {
            $actualEvent = $actualEvents[$key];
            if (get_class($expectedEvent) !== get_class($actualEvent)) {
                return false;
            }

            $constraint = new \PHPUnit_Framework_Constraint_IsEqual($expectedEvent);
            if (!$constraint->evaluate($actualEvent, '', true)) {
                return false;
            }
        }

        return true;
    }

    public function toString()
    {
        return '';
    }
}
