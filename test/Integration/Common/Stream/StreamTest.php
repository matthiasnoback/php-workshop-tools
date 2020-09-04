<?php
declare(strict_types=1);

namespace Test\Integration\Common\Stream;

use Asynchronicity\PHPUnit\Eventually;
use Common\Stream\Stream;
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
    private $helloWorldProducer;

    /**
     * @var Process
     */
    private $fooBarProducer;

    protected function setUp(): void
    {
        $this->streamFilePath = tempnam(__DIR__, 'stream_file_path');

        $this->helloWorldProducer = new Process('php produce_hello_world.php', __DIR__, [
            'STREAM_FILE_PATH' => $this->streamFilePath
        ]);

        $this->fooBarProducer = new Process('php produce_foo_bar.php', __DIR__, [
            'STREAM_FILE_PATH' => $this->streamFilePath
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_used_to_produce_and_consume_messages(): void
    {
        $this->consumer = new Process('php consume.php', __DIR__, [
            'STREAM_FILE_PATH' => $this->streamFilePath
        ]);
        $this->consumer->start();
        $this->helloWorldProducer->run();

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
    public function it_can_consume_starting_with_a_given_index(): void
    {
        /*
         * A special consumer which starts from index 1 (meaning, the second message
         */
        $this->consumer = new Process('php consume_from_index_1.php', __DIR__, [
            'STREAM_FILE_PATH' => $this->streamFilePath
        ]);

        $this->fooBarProducer->run();
        $this->helloWorldProducer->run();
        $this->helloWorldProducer->run();
        $this->consumer->start();

        // give it some time, then check for startup errors
        sleep(1);
        if ($this->consumer->isTerminated()) {
            throw new \RuntimeException('Consumer failed: ' . $this->consumer->getErrorOutput());
        }

        self::assertThat(
            function () {
                return $this->consumer->getOutput() === "hello_world\nhello_world\n";
            },
            new Eventually(5000, 500),
            sprintf('Actual output was: "%s"', $this->consumer->getOutput())
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_env_variable_has_not_been_defined(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('STREAM_FILE_PATH');
        Stream::produce('', []);
    }

    protected function tearDown(): void
    {
        if ($this->consumer instanceof Process) {
            $this->consumer->stop();
        }

        $this->helloWorldProducer->stop();

        $this->fooBarProducer->stop();

        @unlink($this->streamFilePath);
    }
}
