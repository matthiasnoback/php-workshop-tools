<?php
declare(strict_types = 1);

namespace Test\Unit\Serialization\Fixtures;

final class UnsupportedType
{
    /**
     * @var resource
     */
    private $unsupportedType;

    public function __construct()
    {
        $this->unsupportedType = fopen(__FILE__, 'r');
    }
}
