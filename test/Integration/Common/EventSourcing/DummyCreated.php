<?php
declare(strict_types=1);

namespace Test\Integration\Common\EventSourcing;

final class DummyCreated
{
    /**
     * @var DummyId
     */
    private $dummyId;

    public function __construct(DummyId $dummyId)
    {
        $this->dummyId = $dummyId;
    }

    public function dummyId(): DummyId
    {
        return $this->dummyId;
    }
}
