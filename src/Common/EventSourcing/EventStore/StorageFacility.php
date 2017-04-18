<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore;

interface StorageFacility
{
    public function loadEventsOf(string $aggregateType, string $aggregateId): \Iterator;

    public function loadAllEvents(): \Iterator;

    public function append(EventEnvelope $eventEnvelope): void;

    public function deleteAll(): void;
}
