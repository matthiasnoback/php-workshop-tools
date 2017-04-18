<?php
declare(strict_types=1);

namespace Common\EventSourcing\EventStore;

use Common\Persistence\Entity;

final class EventEnvelope implements Entity
{
    private const DATE_TIME_FORMAT = \DateTime::ATOM;

    /**
     * @var string
     */
    private $eventId;

    /**
     * @var string
     */
    private $aggregateType;

    /**
     * @var string
     */
    private $aggregateId;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var string
     */
    private $occurredAt;

    /**
     * @var string
     */
    private $payload;

    public function __construct(
        string $eventId,
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        \DateTimeImmutable $occurredAt,
        string $payload
    ) {
        $this->eventId = $eventId;
        $this->aggregateType = $aggregateType;
        $this->aggregateId = $aggregateId;
        $this->eventType = $eventType;
        $this->occurredAt = $occurredAt->format(self::DATE_TIME_FORMAT);
        $this->payload = $payload;
    }

    public function id()
    {
        return $this->eventId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function aggregateType(): string
    {
        return $this->aggregateType;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventType(): string
    {
        return $this->eventType;
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $this->occurredAt);
    }

    public function payload(): string
    {
        return $this->payload;
    }
}
