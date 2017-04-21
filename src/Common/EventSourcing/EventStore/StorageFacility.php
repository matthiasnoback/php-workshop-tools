<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore;

interface StorageFacility
{
    public function loadEventsOf(string $aggregateType, string $aggregateId): array;

    public function loadAllEvents(): array;

    public function append(EventEnvelope $eventEnvelope): void;

    public function deleteAll(): void;
}
