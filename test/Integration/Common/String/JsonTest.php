<?php
declare(strict_types=1);

namespace Test\Integration\Common\String;

use Common\String\Json;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    /**
     * @test
     */
    public function it_encodes_data()
    {
        self::assertEquals('{"foo":"bar"}', Json::encode(['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function it_decodes_data_and_returns_objects_instead_of_arrays()
    {
        self::assertEquals((object)['foo' => 'bar'], Json::decode('{"foo":"bar"}'));
    }

    /**
     * @test
     */
    public function it_can_return_arrays_if_you_want()
    {
        self::assertEquals(['foo' => 'bar'], Json::decode('{"foo":"bar"}', true));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_upon_an_encoding_error()
    {
        $canNotEncodeThis = fopen(__FILE__, 'r+b');

        $this->expectException(\InvalidArgumentException::class);
        Json::encode($canNotEncodeThis);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_upon_a_decoding_error()
    {
        $this->expectException(\InvalidArgumentException::class);
        Json::decode('{"invalid');
    }
}
