<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

use ECSPrefix20211002\Clue\React\NDJson\Decoder;
use ECSPrefix20211002\Clue\React\NDJson\Encoder;
use Exception;
use ECSPrefix20211002\React\ChildProcess\Process;
use ECSPrefix20211002\React\EventLoop\LoopInterface;
use ECSPrefix20211002\React\EventLoop\TimerInterface;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\Exception\ParallelShouldNotHappenException;
use Throwable;
/**
 * Inspired at @see https://raw.githubusercontent.com/phpstan/phpstan-src/master/src/Parallel/Process.php
 */
final class ParallelProcess
{
    /**
     * @var \React\ChildProcess\Process
     */
    public $process;
    /**
     * @var \Clue\React\NDJson\Encoder
     */
    private $encoder;
    /**
     * @var resource
     */
    private $stdErr;
    /**
     * @var callable(mixed[]) : void
     */
    private $onData;
    /**
     * @var callable(Throwable): void
     */
    private $onError;
    /**
     * @var \React\EventLoop\TimerInterface|null
     */
    private $timer;
    /**
     * @var string
     */
    private $command;
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;
    /**
     * @var int
     */
    private $timetoutInSeconds;
    public function __construct(string $command, \ECSPrefix20211002\React\EventLoop\LoopInterface $loop, int $timetoutInSeconds)
    {
        $this->command = $command;
        $this->loop = $loop;
        $this->timetoutInSeconds = $timetoutInSeconds;
    }
    /**
     * @param callable(mixed[] $onData) : void $onData
     * @param callable(Throwable $onError) : void $onError
     * @param callable(?int $onExit, string $output) : void $onExit
     */
    public function start(callable $onData, callable $onError, callable $onExit) : void
    {
        // todo should I unlink this file after?
        $tmp = \tmpfile();
        if ($tmp === \false) {
            throw new \Symplify\EasyCodingStandard\Parallel\Exception\ParallelShouldNotHappenException('Failed creating temp file.');
        }
        $this->stdErr = $tmp;
        $this->process = new \ECSPrefix20211002\React\ChildProcess\Process($this->command, null, null, [2 => $this->stdErr]);
        $this->process->start($this->loop);
        $this->onData = $onData;
        $this->onError = $onError;
        $this->process->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::EXIT, function ($exitCode) use($onExit) : void {
            $this->cancelTimer();
            \rewind($this->stdErr);
            $onExit($exitCode, \stream_get_contents($this->stdErr));
            \fclose($this->stdErr);
        });
    }
    /**
     * @param mixed[] $data
     */
    public function request(array $data) : void
    {
        $this->cancelTimer();
        $this->encoder->write($data);
        $this->timer = $this->loop->addTimer($this->timetoutInSeconds, function () : void {
            $onError = $this->onError;
            $errorMessage = \sprintf('Child process timed out after %d seconds', $this->timetoutInSeconds);
            $onError(new \Exception($errorMessage));
        });
    }
    public function quit() : void
    {
        $this->cancelTimer();
        if (!$this->process->isRunning()) {
            return;
        }
        foreach ($this->process->pipes as $pipe) {
            $pipe->close();
        }
        $this->encoder->end();
        $this->process->terminate();
    }
    public function bindConnection(\ECSPrefix20211002\Clue\React\NDJson\Decoder $decoder, \ECSPrefix20211002\Clue\React\NDJson\Encoder $encoder) : void
    {
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) : void {
            $this->cancelTimer();
            if ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION] !== \Symplify\EasyCodingStandard\Parallel\Enum\Action::RESULT) {
                return;
            }
            $onData = $this->onData;
            $onData($json['result']);
        });
        $this->encoder = $encoder;
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, function (\Throwable $error) : void {
            $onError = $this->onError;
            $onError($error);
        });
        $encoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, function (\Throwable $error) : void {
            $onError = $this->onError;
            $onError($error);
        });
    }
    private function cancelTimer() : void
    {
        if ($this->timer === null) {
            return;
        }
        $this->loop->cancelTimer($this->timer);
        $this->timer = null;
    }
}
