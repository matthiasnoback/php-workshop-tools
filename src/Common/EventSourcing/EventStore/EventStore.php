<?php
declare(strict_types = 1);

namespace Common\EventSourcing\EventStore;

use Common\EventDispatcher\EventDispatcher;
use NaiveSerializer\JsonSerializer;
use Ramsey\Uuid\Uuid;

final class EventStore
{
    /**
     * @var StorageFacility
     */
    private $storageFacility;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var JsonSerializer
     */
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
            $id = (string)Uuid::uuid4();
            $eventType = get_class($event);
            $payload = $this->extractPayload($event);
            $now = new \DateTimeImmutable();
            // create a sortable representation
            $createdAt = $now->format('Y-m-d H:i:s ') . str_pad($now->format('u'), 6, '0');
            $this->storageFacility->persistRawEvent(
                [
                    'event_type' => $eventType,
                    'event_id' => $id,
                    'payload' => $payload,
                    'aggregate_type' => $aggregateType,
                    'aggregate_id' => (string)$aggregateId,
                    'created_at' => $createdAt
                ]
            );

            $this->eventDispatcher->dispatch($event);
        }
    }

    /**
     * @param string $aggregateType
     * @param string $aggregateId
     * @return \Iterator
     */
    public function loadEvents(string $aggregateType, string $aggregateId): \Iterator
    {
        foreach ($this->storageFacility->loadRawEvents($aggregateType, $aggregateId) as $rawEvent) {
            yield $this->restoreEvent($rawEvent);
        }
    }

    /**
     * Load all previous events and dispatch them to the provided EventDispatcher.
     *
     * @param EventDispatcher $eventDispatcher
     */
    public function replayHistory(EventDispatcher $eventDispatcher): void
    {
        $allEvents = $this->storageFacility->loadAllRawEvents();
        foreach ($allEvents as $rawEvent) {
            $eventDispatcher->dispatch($this->restoreEvent($rawEvent));
        }
    }

    /**
     * @param string $aggregateType
     * @param string $aggregateId
     * @return object Of type $aggregateType
     */
    public function reconstitute(string $aggregateType, string $aggregateId)
    {
        return call_user_func([$aggregateType, 'reconstitute'], $this->loadEvents($aggregateType, $aggregateId));
    }

    private function extractPayload($event): string
    {
        return $this->serializer->serialize($event);
    }

    /**
     * @param array $rawEvent
     * @return object Of type $rawEvent['event_type']
     */
    private function restoreEvent(array $rawEvent)
    {
        return $this->serializer->deserialize($rawEvent['event_type'], $rawEvent['payload']);
    }
}
