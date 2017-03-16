<?php
declare(strict_types = 1);

namespace Test\Unit\DomainModel;

use Test\Unit\DomainModel\Fixtures\DummyAggregateId;

final class AggregateIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_converted_to_and_from_a_string()
    {
        $id = 'some string';
        $aggregateId = DummyAggregateId::fromString($id);

        $this->assertEquals($id, (string)$aggregateId);
    }
}
