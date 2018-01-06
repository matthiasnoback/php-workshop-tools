<?php
declare(strict_types = 1);

namespace Test\Unit\DomainModel;

use PHPUnit\Framework\TestCase;
use Test\Unit\DomainModel\Fixtures\DummyAggregateRoot;
use Test\Unit\DomainModel\Fixtures\DummyDomainEvent;

final class AggregateRootTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_record_and_clear_events()
    {
        $aggregateRoot = new DummyAggregateRoot();
        $event1 = new DummyDomainEvent();
        $aggregateRoot->recordThisEvent($event1);
        $event2 = new DummyDomainEvent();
        $aggregateRoot->recordThisEvent($event2);

        $this->assertSame([$event1, $event2], $aggregateRoot->recordedEvents());

        $aggregateRoot->clearEvents();

        $this->assertSame([], $aggregateRoot->recordedEvents());
    }
}
