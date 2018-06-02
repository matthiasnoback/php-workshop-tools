<?php
declare(strict_types = 1);

namespace Common\Persistence;

interface IdentifiableObject
{
    /**
     * @return string Or object with __toString() method
     */
    public function id();
}
