<?php
declare(strict_types = 1);

namespace Test\Unit\Serialization\Fixtures;

final class SupportedCases
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
     * @var SupportedCases[]
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
