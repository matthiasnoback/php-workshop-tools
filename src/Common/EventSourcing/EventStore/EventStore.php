<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore;

use Common\EventDispatcher\EventDispatcher;
use NaiveSerializer\JsonSerializer;
use Ramsey\Uuid\Uuid;

final class EventStore
{
    private $storageFacility;
    private $eventDispatcher;
    private $serializer;

    public function __construct(StorageFacility $storageFacility, EventDispatcher $eventDispatcher, JsonSerializer $serializer)
    {
        $this->storageFacility = $storageFacility;
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
    }

    public function append(string $aggregateType, string $aggregateId, array $events): void
    {
        foreach ($events as $event) {
            $envelope = $this->wrapInEnvelope($aggregateType, $aggregateId, $event);
            $this->storageFacility->append($envelope);

            $this->eventDispatcher->dispatch($event);
        }
    }

    private function wrapInEnvelope(string $aggregateType, string $aggregateId, $event): EventEnvelope
    {
        $id = (string)Uuid::uuid4();
        $eventType = get_class($event);
        $payload = $this->extractPayload($event);
        $now = new \DateTimeImmutable();

        return new EventEnvelope(
            $id,
            $aggregateType,
            $aggregateId,
            $eventType,
            $now,
            $payload
        );
    }

    public function loadEvents(string $aggregateType, string $aggregateId): array
    {
        $events = [];

        foreach ($this->storageFacility->loadEventsOf($aggregateType, $aggregateId) as $rawEvent) {
            $events[] = $this->restoreEvent($rawEvent);
        }

        return $events;
    }

    /**
     * Load all previous events and dispatch them to the provided EventDispatcher.
     *
     * @param EventDispatcher $eventDispatcher
     */
    public function replayHistory(EventDispatcher $eventDispatcher): void
    {
        $allEvents = $this->storageFacility->loadAllEvents();
        foreach ($allEvents as $rawEvent) {
            $eventDispatcher->dispatch($this->restoreEvent($rawEvent));
        }
    }

    public function reconstitute(string $aggregateType, string $aggregateId)
    {
        $events = $this->loadEvents($aggregateType, $aggregateId);

        if (empty($events)) {
            throw AggregateNotFound::withClassAndId(
                $aggregateType,
                $aggregateId
            );
        }

        return call_user_func([$aggregateType, 'reconstitute'], $events);
    }

    private function extractPayload($event): string
    {
        return $this->serializer->serialize($event);
    }

    /**
     * @param EventEnvelope $eventEnvelope
     * @return object Of type $eventEnvelope->eventType()
     */
    private function restoreEvent(EventEnvelope $eventEnvelope)
    {
        return $this->serializer->deserialize($eventEnvelope->eventType(), $eventEnvelope->payload());
    }
}
