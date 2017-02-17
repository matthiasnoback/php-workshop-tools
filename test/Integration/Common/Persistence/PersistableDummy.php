<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Common\Persistence\Entity;
use Common\Persistence\Id;

final class PersistableDummy implements Entity
{
    /**
     * @var DummyId
     */
    private $id;

    /**
     * @var string
     */
    private $secretValue;

    public function __construct(DummyId $id)
    {
        $this->id = $id;
        $this->secretValue = uniqid();
    }

    public function id() : Id
    {
        return $this->id;
    }
}
