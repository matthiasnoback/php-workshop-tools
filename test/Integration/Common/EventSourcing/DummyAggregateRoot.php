<?php
declare(strict_types=1);

namespace Test\Integration\Common\EventSourcing;

use Common\EventSourcing\Aggregate\EventSourcedAggregate;
use Common\EventSourcing\Aggregate\EventSourcedAggregateCapabilities;

final class DummyAggregateRoot implements EventSourcedAggregate
{
    use EventSourcedAggregateCapabilities;

    public static function create(DummyId $dummyId): DummyAggregateRoot
    {
        $dummy = new static();
        $dummy->recordThat(new DummyCreated($dummyId));

        return $dummy;
    }

    public function rename($newName)
    {
        $this->recordThat(new DummyRenamed($this->id, $newName));
    }

    private function whenDummyCreated(DummyCreated $event)
    {
        $this->id = $event->dummyId();
    }

    private function whenDummyRenamed()
    {

    }
}
