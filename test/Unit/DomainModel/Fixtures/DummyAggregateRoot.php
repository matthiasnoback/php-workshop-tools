<?php
declare(strict_types = 1);

namespace Test\Unit\DomainModel\Fixtures;

use Common\DomainModel\AggregateRoot;

final class DummyAggregateRoot
{
    use AggregateRoot;

    public function recordThisEvent($event)
    {
        $this->recordThat($event);
    }
}
