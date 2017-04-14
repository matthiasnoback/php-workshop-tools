<?php
declare(strict_types=1);

namespace Test\Integration\Common\EventSourcing;

final class DummyRenamed
{
    /**
     * @var DummyId
     */
    private $dummyId;

    /**
     * @var string
     */
    private $name;

    public function __construct(DummyId $dummyId, string $name)
    {
        $this->dummyId = $dummyId;
        $this->name = $name;
    }

    public function dummyId()
    {
        return $this->dummyId;
    }

    public function name()
    {
        return $this->name;
    }
}
