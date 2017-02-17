<?php
declare(strict_types = 1);

namespace Common\Persistence;

interface Entity
{
    public function id() : Id;
}
