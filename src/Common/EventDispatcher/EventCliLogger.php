<?php
declare(strict_types = 1);

namespace Common\EventDispatcher;

use function Common\CommandLine\line;
use function Common\CommandLine\make_green;

final class EventCliLogger
{
    public function __invoke($event)
    {
        line(make_green('Received an event'));

        dump($event);
    }
}
