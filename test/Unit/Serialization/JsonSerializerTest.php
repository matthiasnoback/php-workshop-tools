<?php
declare(strict_types = 1);

namespace Test\Unit\Serialization;

use Common\Serialization\JsonSerializer;

class FlatClass
{
    /**
     * @var string
     */
    public $a;

    /**
     * @var int
     */
    public $b;

    /**
     * @var FlatClass[]
     */
    public $c = [];

    /**
     * @var bool
     */
    public $d;

    /**
     * @var float
     */
    public $e;
}

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_serializes_and_deserializes_a_json_object()
    {
        $original = new FlatClass();
        $original->a = 'a';
        $original->b = 1;
        $originalSub = new FlatClass();
        $originalSub->a = 'a1';
        $originalSub->b = 2;
        $original->c = [$originalSub];
        $original->d = true;
        $original->e = 1.23;

        $serialized = JsonSerializer::serialize($original);

        $expectedJson = <<<EOD
{
    "a": "a",
    "b":1,
    "c": [
        {
            "a": "a1",
            "b": 2,
            "c": [],
            "d": null,
            "e": null
        }
    ],
    "d": true,
    "e": 1.23
}
EOD;

        $this->assertJsonStringEqualsJsonString($expectedJson, $serialized);

        $deserialized = JsonSerializer::deserialize(FlatClass::class, $serialized);

        $this->assertEquals($original, $deserialized);
    }
}
