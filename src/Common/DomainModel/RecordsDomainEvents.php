<?php
declare(strict_types = 1);

namespace Common\DomainModel;

interface RecordsDomainEvents
{
    /**
     * @return object[]
     */
    public function recordedEvents();
}
