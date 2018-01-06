<?php
declare(strict_types = 1);

namespace Common\DomainModel;

use Assert\Assertion;

trait AggregateId
{
    /**
     * @var string
     */
    private $id;

    private function __construct()
    {
    }

    /**
     * @param string $id
     * @return static
     */
    public static function fromString(string $id)
    {
        Assertion::notEmpty($id);
        Assertion::uuid($id);

        $aggregateId = new static();
        $aggregateId->id = $id;

        return $aggregateId;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals($otherAggregateId): bool
    {
        return get_class($otherAggregateId) === get_class($this)
            && (string)$this === (string)$otherAggregateId;
    }
}
