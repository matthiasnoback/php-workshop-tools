<?php
declare(strict_types=1);

namespace Common\Persistence;

interface CanBePersisted
{
    /**
     * @return string|object Either a string or an object with a `__toString()` method
     */
    public function id();
}
