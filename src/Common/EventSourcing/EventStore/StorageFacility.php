<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore;

interface StorageFacility
{
    /**
     * @return void
     */
    public function setUp(): void;

    /**
     * @param string $aggregateType
     * @param string $aggregateId
     * @return \Iterator
     */
    public function loadRawEvents(string $aggregateType, string $aggregateId): \Iterator;

    /**
     * @return \Iterator
     */
    public function loadAllRawEvents(): \Iterator;

    /**
     * @param array $rawEventData
     */
    public function persistRawEvent(array $rawEventData): void;
}
