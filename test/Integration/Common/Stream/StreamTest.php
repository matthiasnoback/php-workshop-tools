<?php
declare(strict_types=1);

namespace Test\Integration\Common\Stream;

use Common\Stream\Stream;
use Matthias\PhpUnitAsynchronicity\Eventually;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class StreamTest extends TestCase
{
    /**
     * @var string
     */
    private $streamFilePath;

    /**
     * @var Process
     */
    private $consumer;

    /**
     * @var Process
     */
    private $producer;

    protected function setUp()
    {
        $this->streamFilePath = tempnam(__DIR__, 'stream_file_path');

        $this->consumer = new Process('php consume.php', __DIR__, [
            'STREAM_FILE_PATH' => $this->streamFilePath
        ]);

        $this->producer = new Process('php produce.php', __DIR__, [
            'STREAM_FILE_PATH' => $this->streamFilePath
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_used_to_produce_and_consume_messages()
    {
        $this->consumer->start();
        $this->producer->run();

        // give it some time, then check for startup errors
        sleep(1);
        if ($this->consumer->isTerminated()) {
            throw new \RuntimeException('Consumer failed: ' . $this->consumer->getErrorOutput());
        }

        self::assertThat(function () {
            return strpos($this->consumer->getIncrementalOutput(), 'Hello, world!') !== false;
        }, new Eventually(5000, 500));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_env_variable_has_not_been_defined()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('STREAM_FILE_PATH');
        Stream::produce('');
    }

    protected function tearDown()
    {
        $this->consumer->stop();
        $this->producer->stop();
        @unlink($this->streamFilePath);
    }
}
