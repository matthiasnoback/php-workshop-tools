<?php
declare(strict_types = 1);

namespace Common\EventSourcing\Aggregate\Repository;

use Assert\Assertion;
use Common\EventSourcing\Aggregate\EventSourcedAggregate;
use Common\EventSourcing\EventStore\EventStore;

final class EventSourcedAggregateRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var string
     */
    private $aggregateType;

    public function __construct(EventStore $eventStore, $aggregateType)
    {
        $this->eventStore = $eventStore;
        $this->aggregateType = $aggregateType;
    }

    public function save(EventSourcedAggregate $aggregate)
    {
        Assertion::same(get_class($aggregate), $this->aggregateType);

        $this->eventStore->append($this->aggregateType, $aggregate->id(), $aggregate->popRecordedEvents());
    }

    /**
     * @param string $id
     * @return object Of type $this->aggregateType
     */
    public function getById(string $id)
    {
        return $this->eventStore->reconstitute($this->aggregateType, $id);
    }
}
