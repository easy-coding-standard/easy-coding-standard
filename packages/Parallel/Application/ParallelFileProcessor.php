<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use ECSPrefix20210619\Clue\React\NDJson\Decoder;
use ECSPrefix20210619\Clue\React\NDJson\Encoder;
use ECSPrefix20210619\React\ChildProcess\Process;
use ECSPrefix20210619\React\EventLoop\StreamSelectLoop;
use ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
use Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider;
use Throwable;
/**
 * @see https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92#diff-39c7a3b0cbb217bbfff96fbb454e6e5e60c74cf92fbb0f9d246b8bebbaad2bb0
 */
final class ParallelFileProcessor
{
    /**
     * @var string
     */
    const ACTION = 'action';
    /**
     * @var string
     */
    const CHECK = 'check';
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory
     */
    private $workerCommandLineFactory;
    public function __construct(\ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory $workerCommandLineFactory)
    {
        $this->parameterProvider = $parameterProvider;
        $this->workerCommandLineFactory = $workerCommandLineFactory;
    }
    /**
     * @param Closure(int):void|null $postFileCallback
     * @return array{FileError}
     * @param string|null $projectConfigFile
     */
    public function analyse(\Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule $schedule, string $mainScript, $postFileCallback, $projectConfigFile, \ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : array
    {
        $systemErrorsCountLimit = $this->parameterProvider->provideIntParameter(\Symplify\EasyCodingStandard\ValueObject\Option::SYSTEM_ERROR_COUNT_LIMIT);
        $jobs = \array_reverse($schedule->getJobs());
        $streamSelectLoop = new \ECSPrefix20210619\React\EventLoop\StreamSelectLoop();
        $processes = [];
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        $errors = [];
        $systemErrors = [];
        $command = $this->workerCommandLineFactory->create($mainScript, $projectConfigFile, $input);
        $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = \false;
        $handleError = static function (\Throwable $error) use($streamSelectLoop, &$systemErrors, &$systemErrorsCount, &$reachedSystemErrorsCountLimit) {
            $streamSelectLoop = null;
            $systemErrors[] = 'System error: ' . $error->getMessage();
            ++$systemErrorsCount;
            $reachedSystemErrorsCountLimit = \true;
            $streamSelectLoop->stop();
        };
        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            if ($jobs === []) {
                break;
            }
            $childProcess = new \ECSPrefix20210619\React\ChildProcess\Process($command);
            $childProcess->start($streamSelectLoop);
            $processStdInEncoder = new \ECSPrefix20210619\Clue\React\NDJson\Encoder($childProcess->stdin);
            $processStdInEncoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleError);
            $processStdOutDecoder = new \ECSPrefix20210619\Clue\React\NDJson\Decoder($childProcess->stdout, \true, 512, 0, 4 * 1024 * 1024);
            $processStdOutDecoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) use($childProcess, &$systemErrors, &$errors, &$jobs, $processStdInEncoder, $postFileCallback, &$systemErrorsCount, &$reachedSystemErrorsCountLimit, $streamSelectLoop) {
                $systemErrorsCountLimit = null;
                $streamSelectLoop = null;
                // @todo
                foreach ($json['errors'] as $jsonError) {
                    if (\is_string($jsonError)) {
                        $systemErrors[] = \sprintf('System error: %s', $jsonError);
                        continue;
                    }
                    $errors[] = \Symplify\EasyCodingStandard\Parallel\Application\Error::decode($jsonError);
                }
                if ($postFileCallback !== null) {
                    $postFileCallback($json['files_count']);
                }
                $systemErrorsCount += $json['system_errors_count'];
                if ($systemErrorsCount >= $systemErrorsCountLimit) {
                    $reachedSystemErrorsCountLimit = \true;
                    $streamSelectLoop->stop();
                }
                if ($jobs === []) {
                    foreach ($childProcess->pipes as $pipe) {
                        $pipe->close();
                    }
                    $processStdInEncoder->write([self::ACTION => 'quit']);
                    return;
                }
                $job = \array_pop($jobs);
                $processStdInEncoder->write([self::ACTION => self::CHECK, 'files' => $job]);
            });
            $processStdOutDecoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleError);
            $stdErrStreamBuffer = new \Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer($childProcess->stderr);
            $childProcess->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::EXIT, static function ($exitCode) use(&$systemErrors, $stdErrStreamBuffer) {
                if ($exitCode === 0) {
                    return;
                }
                $systemErrors[] = \sprintf('Child process error: %s', $stdErrStreamBuffer->getBuffer());
            });
            $job = \array_pop($jobs);
            $processStdInEncoder->write([self::ACTION => self::CHECK, 'files' => $job]);
            $processes[] = $childProcess;
        }
        $streamSelectLoop->run();
        if ($reachedSystemErrorsCountLimit) {
            $systemErrors[] = \sprintf('Reached system errors count limit of %d, exiting...', $systemErrorsCountLimit);
        }
        return [
            'errors' => $errors,
            // @todo
            'file_diffs' => $fileDiffs ?? [],
            'system_errors' => $systemErrors,
        ];
    }
}
