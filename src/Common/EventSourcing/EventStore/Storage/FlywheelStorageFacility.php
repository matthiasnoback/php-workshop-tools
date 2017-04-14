<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore\Storage;

use Common\EventSourcing\EventStore\StorageFacility;
use JamesMoss\Flywheel\Config;
use JamesMoss\Flywheel\Document;
use JamesMoss\Flywheel\Repository;

final class FlywheelStorageFacility implements StorageFacility
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(string $databaseDirectory)
    {
        $config = new Config($databaseDirectory);
        $this->repository = new Repository('events', $config);
    }

    public function setUp(): void
    {
    }

    public function loadRawEvents(string $aggregateType, string $aggregateId): \Iterator
    {
        $documents = $this->repository->query()
            ->andWhere('aggregate_type', '==', $aggregateType)
            ->andWhere('aggregate_id', '==', $aggregateId)
            ->orderBy('created_at ASC')
            ->execute();

        foreach ($documents as $document) {
            yield get_object_vars($document);
        }
    }

    public function loadAllRawEvents(): \Iterator
    {
        $documents = $this->repository->query()
            ->orderBy('created_at ASC')
            ->execute();

        foreach ($documents as $document) {
            yield get_object_vars($document);
        }
    }

    public function persistRawEvent(array $rawEventData): void
    {
        $document = new Document($rawEventData);
        $document->setId($rawEventData['event_id']);

        $this->repository->store($document);
    }

    public function reset(): void
    {
        foreach ($this->repository->getAllFiles() as $file) {
            unlink((string)$file);
        }
    }
}
