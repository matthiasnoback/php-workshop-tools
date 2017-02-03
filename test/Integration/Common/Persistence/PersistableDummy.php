<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Common\Persistence\CanBePersisted;

final class PersistableDummy implements CanBePersisted
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $secretValue;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->secretValue = uniqid();
    }

    public function id() : Uuid
    {
        return Uuid::fromString($this->id);
    }
}
