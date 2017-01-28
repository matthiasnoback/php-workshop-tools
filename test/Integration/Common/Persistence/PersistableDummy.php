<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Ramsey\Uuid\UuidInterface;
use Common\Persistence\CanBePersisted;

final class PersistableDummy implements CanBePersisted
{
    private $id;
    private $secretValue;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->secretValue = uniqid();
    }

    public function id() : UuidInterface
    {
        return $this->id;
    }
}
