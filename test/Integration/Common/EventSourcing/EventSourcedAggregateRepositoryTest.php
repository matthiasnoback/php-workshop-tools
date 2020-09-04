<?php
declare(strict_types=1);

namespace Test\Integration\Common\EventSourcing;

use Common\EventDispatcher\EventDispatcher;
use Common\EventSourcing\Aggregate\Repository\EventSourcedAggregateRepository;
use Common\EventSourcing\EventStore\AggregateNotFound;
use Common\EventSourcing\EventStore\EventStore;
use Common\EventSourcing\EventStore\Storage\DatabaseStorageFacility;
use NaiveSerializer\JsonSerializer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class EventSourcedAggregateRepositoryTest extends TestCase
{
    /**
     * @var EventSourcedAggregateRepository
     */
    private $repository;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var EventStore
     */
    private $eventStore;

    protected function setUp(): void
    {
        $storageFacility = new DatabaseStorageFacility();
        $storageFacility->deleteAll();

        $this->eventDispatcher = new EventDispatcher();
        $this->eventStore = new EventStore($storageFacility, $this->eventDispatcher, new JsonSerializer());
        $this->repository = new EventSourcedAggregateRepository($this->eventStore, DummyAggregateRoot::class);
    }

    /**
     * @test
     */
    public function it_persists_and_reconstitutes_an_aggregate_root()
    {
        $dummyId = DummyId::fromString((string)Uuid::uuid4());
        $aggregateRoot = DummyAggregateRoot::create($dummyId);
        $aggregateRoot->rename('New name');

        $this->repository->save($aggregateRoot);

        $reconstitutedDummy = $this->repository->getById((string)$dummyId);

        $this->assertEquals($aggregateRoot, $reconstitutedDummy);
    }

    /**
     * @test
     */
    public function when_persisting_event_subscribers_get_notified()
    {
        $dispatchedEvents = [];
        $this->eventDispatcher->subscribeToAllEvents(function ($event) use (&$dispatchedEvents) {
            $dispatchedEvents[] = $event;
        });

        $dummyId = DummyId::fromString((string)Uuid::uuid4());
        $newName = 'New name';
        $aggregateRoot = DummyAggregateRoot::create($dummyId);
        $aggregateRoot->rename($newName);

        $this->repository->save($aggregateRoot);

        $this->assertEquals(
            [
                new DummyCreated($dummyId),
                new DummyRenamed($dummyId,'New name')
            ],
            $dispatchedEvents
        );
    }

    /**
     * @test
     */
    public function when_replaying_all_events_will_be_dispatched()
    {
        $dispatchedEvents = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeToAllEvents(function ($event) use (&$dispatchedEvents) {
            $dispatchedEvents[] = $event;
        });

        $dummyId1 = DummyId::fromString((string)Uuid::uuid4());
        $dummyId2 = DummyId::fromString((string)Uuid::uuid4());
        $dummy1 = DummyAggregateRoot::create($dummyId1);
        $this->repository->save($dummy1);

        $dummy2 = DummyAggregateRoot::create($dummyId2);
        $this->repository->save($dummy2);

        $this->eventStore->replayHistory($eventDispatcher);

        $this->assertEquals(
            [
                new DummyCreated($dummyId1),
                new DummyCreated($dummyId2)
            ],
            $dispatchedEvents
        );
    }

    /**
     * @test
     */
    public function when_no_previous_events_are_known_it_fails_to_reconstitute()
    {
        $unknownId = (string)Uuid::uuid4();

        $this->expectException(AggregateNotFound::class);
        $this->eventStore->reconstitute(DummyAggregateRoot::class, $unknownId);
    }
}
