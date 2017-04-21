<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore\Storage;

use Common\EventSourcing\EventStore\EventEnvelope;
use Common\EventSourcing\EventStore\StorageFacility;
use Common\Persistence\Database;

final class DatabaseStorageFacility implements StorageFacility
{
    public function loadEventsOf(string $aggregateType, string $aggregateId): array
    {
        return array_filter(
            $this->loadAllEvents(),
            function (EventEnvelope $eventEnvelope) use ($aggregateId, $aggregateType) {
                return $eventEnvelope->aggregateType() === $aggregateType
                    && $eventEnvelope->aggregateId() === $aggregateId;
            }
        );
    }

    public function loadAllEvents(): array
    {
        return Database::retrieveAll(EventEnvelope::class);
    }

    public function append(EventEnvelope $eventEnvelope): void
    {
        Database::persist($eventEnvelope);
    }

    public function deleteAll(): void
    {
        Database::deleteAll(EventEnvelope::class);
    }
}
