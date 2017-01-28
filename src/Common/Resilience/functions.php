<?php
declare(strict_types=1);

namespace Common\Resilience;

function retry(int $remainingAttempts, int $waitMs, callable $action)
{
    try {
        $action();
    } catch (\Throwable $fault) {
        if ($remainingAttempts == 1) {
            throw $fault;
        }

        usleep($waitMs * 1000);
        retry($remainingAttempts - 1, $waitMs, $action);
    }
}
