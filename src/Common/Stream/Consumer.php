<?php
declare(strict_types=1);

namespace Common\Stream;

use Assert\Assertion;
use Common\String\Json;
use Symfony\Component\Process\Process;

final class Consumer
{
    /**
     * @var string
     */
    private $streamFilePath;

    public function __construct(string $streamFilePath)
    {
        $this->streamFilePath = $streamFilePath;
    }

    public function consume(callable $callback, int $startAtIndex): void
    {
        Assertion::greaterOrEqualThan($startAtIndex, 0, 'The consumer can only start consuming at index 0 or greater');

        // read all of the stream at once, then keep following new additions
        $location = 1 + $startAtIndex;
        $process = new Process(sprintf('tail -f -n +%d %s', $location, $this->streamFilePath));

        // never stop
        $process->setTimeout(null);

        // don't forward output, let the callback deal with it
        $process->disableOutput();

        $process->start(function ($type, $data) use ($callback) {
            if ($type === Process::OUT) {
                $lines = explode("\n", $data);
                foreach ($lines as $line) {
                    if ($line === '') {
                        continue;
                    }

                    $decodedMessage = Json::decode($line, true);
                    $callback($decodedMessage->messageType, $decodedMessage->data);
                }
            }

            if ($type === Process::ERR) {
                throw new \RuntimeException('ERR: ' . $data);
            }
        });

        // wait until `tail` terminates
        $process->wait();
    }
}
