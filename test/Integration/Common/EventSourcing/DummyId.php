<?php
declare(strict_types=1);

namespace Test\Integration\Common\EventSourcing;

use Common\DomainModel\AggregateId;

final class DummyId
{
    use AggregateId;
}
