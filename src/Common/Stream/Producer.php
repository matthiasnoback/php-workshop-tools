<?php
declare(strict_types=1);

namespace Common\Stream;

use Common\Persistence\Filesystem;

final class Producer
{
    /**
     * @var string
     */
    private $streamFilePath;

    public function __construct(string $streamFilePath)
    {
        $this->streamFilePath = $streamFilePath;
    }

    public function produce(string $message): void
    {
        file_put_contents($this->streamFilePath, $message . "\n", FILE_APPEND);
    }
}
