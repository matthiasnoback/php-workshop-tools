<?php
declare(strict_types=1);

namespace Test\Integration\Common\Stream;

use Matthias\PhpUnitAsynchronicity\Eventually;
use Matthias\Polling\CallableProbe;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class StreamTest extends TestCase
{
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
        $streamFilePath = tempnam(sys_get_temp_dir(), 'stream_file_path');

        $this->consumer = new Process('php consume.php', __DIR__, [
            'STREAM_FILE_PATH' => $streamFilePath
        ]);

        $this->producer = new Process('php produce.php', __DIR__, [
            'STREAM_FILE_PATH' => $streamFilePath
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_used_to_produce_and_consume_messages()
    {
        $this->consumer->start();
        $this->producer->run();

        self::assertThat(function () {
            return strpos($this->consumer->getIncrementalOutput(), 'Hello, world!') !== false;
        }, new Eventually(10000, 500));
    }

    protected function tearDown()
    {
        $this->consumer->stop();
        $this->producer->stop();
    }
}
