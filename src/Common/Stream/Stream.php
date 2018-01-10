<?php
declare(strict_types=1);

namespace Common\Stream;

use Assert\Assertion;
use Common\Persistence\Filesystem;

/**
 * Using `Stream::produce()` you can write lines to a file. Using
 * `Stream::consume()` you can consume these lines one by one, starting with
 * the first line ever produced.
 */
final class Stream
{
    private const ENV_STREAM_FILE_PATH = 'STREAM_FILE_PATH';

    public static function consume(callable $callback): void
    {
        (new Consumer(self::getStreamFilePath()))->consume($callback);
    }

    public static function produce(string $message): void
    {
        (new Producer(self::getStreamFilePath()))->produce($message);
    }

    private static function getStreamFilePath(): string
    {
        $streamFilePath = getenv(self::ENV_STREAM_FILE_PATH);
        if ($streamFilePath === false) {
            throw new \RuntimeException(sprintf('Environment variable "%s" should be set', self::ENV_STREAM_FILE_PATH));
        }

        if (!is_file($streamFilePath)) {
            Filesystem::ensureFilePathIsWritable($streamFilePath);
            touch($streamFilePath);
        }

        return realpath($streamFilePath);
    }
}
