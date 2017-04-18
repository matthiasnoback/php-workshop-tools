<?php
declare(strict_types = 1);

namespace Common\Persistence;

interface Entity
{
    /**
     * @return string Or object with __toString() method
     */
    public function id();
}
