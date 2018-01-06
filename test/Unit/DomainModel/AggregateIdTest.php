<?php
declare(strict_types = 1);

namespace Test\Unit\DomainModel;

use PHPUnit\Framework\TestCase;
use Test\Unit\DomainModel\Fixtures\DummyAggregateId;

final class AggregateIdTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_converted_to_and_from_a_string()
    {
        $id = 'a3b1d6b4-fb04-4f9e-b208-2056b09c5f5b';
        $aggregateId = DummyAggregateId::fromString($id);

        $this->assertEquals($id, (string)$aggregateId);
    }

    /**
     * @test
     */
    public function it_accepts_only_uuid_strings()
    {
        $this->expectException(\InvalidArgumentException::class);

        DummyAggregateId::fromString('not a UUID');
    }
}
