<?php
declare(strict_types=1);

namespace Test\Unit\Web\Fixtures;

final class Application
{
    public function indexController()
    {
        return __METHOD__;
    }

    public function someController()
    {
        return __METHOD__;
    }

    public function withArgumentsController(string $id, string $orderId)
    {
        return func_get_args();
    }
}
