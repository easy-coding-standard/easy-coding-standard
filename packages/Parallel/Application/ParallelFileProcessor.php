<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use ECSPrefix20210825\Clue\React\NDJson\Decoder;
use ECSPrefix20210825\Clue\React\NDJson\Encoder;
use ECSPrefix20210825\React\ChildProcess\Process;
use ECSPrefix20210825\React\EventLoop\StreamSelectLoop;
use ECSPrefix20210825\Symfony\Component\Console\Command\Command;
use ECSPrefix20210825\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
use Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210825\Symplify\PackageBuilder\Parameter\ParameterProvider;
use Throwable;
/**
 * @see https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92#diff-39c7a3b0cbb217bbfff96fbb454e6e5e60c74cf92fbb0f9d246b8bebbaad2bb0
 */
final class ParallelFileProcessor
{
    /**
     * @var string
     */
    private const ACTION = 'action';
    /**
     * @var int
     */
    private $systemErrorsCountLimit;
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory
     */
    private $workerCommandLineFactory;
    public function __construct(\ECSPrefix20210825\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory $workerCommandLineFactory)
    {
        $this->workerCommandLineFactory = $workerCommandLineFactory;
        $this->systemErrorsCountLimit = $parameterProvider->provideIntParameter(\Symplify\EasyCodingStandard\ValueObject\Option::SYSTEM_ERROR_COUNT_LIMIT);
    }
    /**
     * @param Closure(int): void|null $postFileCallback Used for progress bar jump
     * @return mixed[]
     */
    public function analyse(\Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule $schedule, string $mainScript, \Closure $postFileCallback, ?string $projectConfigFile, \ECSPrefix20210825\Symfony\Component\Console\Input\InputInterface $input) : array
    {
        $jobs = \array_reverse($schedule->getJobs());
        $streamSelectLoop = new \ECSPrefix20210825\React\EventLoop\StreamSelectLoop();
        // basic properties setup
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        $codingStandardErrors = [];
        $fileDiffs = [];
        $systemErrors = [];
        $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = \false;
        $workerCommandLine = $this->workerCommandLineFactory->create($mainScript, $projectConfigFile, $input);
        $handleErrorCallable = static function (\Throwable $throwable) use($streamSelectLoop, &$systemErrors, &$systemErrorsCount, &$reachedSystemErrorsCountLimit) : void {
            $systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            ++$systemErrorsCount;
            $reachedSystemErrorsCountLimit = \true;
            $streamSelectLoop->stop();
        };
        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            // nothing else to process, stop now
            if ($jobs === []) {
                break;
            }
            $childProcess = new \ECSPrefix20210825\React\ChildProcess\Process($workerCommandLine);
            $childProcess->start($streamSelectLoop);
            // handlers converting objects to json string
            // @see https://freesoft.dev/program/64329369#encoder
            $processStdInEncoder = new \ECSPrefix20210825\Clue\React\NDJson\Encoder($childProcess->stdin);
            $processStdInEncoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallable);
            // handlers converting string json to array
            // @see https://freesoft.dev/program/64329369#decoder
            $processStdOutDecoder = new \ECSPrefix20210825\Clue\React\NDJson\Decoder($childProcess->stdout, \true, 512, 0, 4 * 1024 * 1024);
            $processStdOutDecoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) use($childProcess, &$systemErrors, &$codingStandardErrors, &$fileDiffs, &$jobs, $processStdInEncoder, $postFileCallback, &$systemErrorsCount, &$reachedSystemErrorsCountLimit, $streamSelectLoop) : void {
                // unpack coding standard errors and file diffs from subprocess to objects here
                foreach ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] as $codingStandardErrorJson) {
                    $codingStandardErrors[] = \Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError::decode($codingStandardErrorJson);
                }
                foreach ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS] as $fileDiffsJson) {
                    $fileDiffs[] = \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff::decode($fileDiffsJson);
                }
                // invoke after the file is processed, e.g. to increase progress bar
                $postFileCallback($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT]);
                $systemErrorsCount += $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT];
                if ($systemErrorsCount >= $this->systemErrorsCountLimit) {
                    $reachedSystemErrorsCountLimit = \true;
                    $streamSelectLoop->stop();
                }
                // all jobs are finished → close everything and quite
                if ($jobs === []) {
                    foreach ($childProcess->pipes as $pipe) {
                        $pipe->close();
                    }
                    $processStdInEncoder->write([self::ACTION => \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::QUIT]);
                    return;
                }
                // start a new job
                $job = \array_pop($jobs);
                $processStdInEncoder->write([self::ACTION => \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::CHECK, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES => $job]);
            });
            $processStdOutDecoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallable);
            $stdErrStreamBuffer = new \Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer($childProcess->stderr);
            $childProcess->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::EXIT, static function ($exitCode) use(&$systemErrors, $stdErrStreamBuffer) : void {
                if ($exitCode === \ECSPrefix20210825\Symfony\Component\Console\Command\Command::SUCCESS) {
                    return;
                }
                $systemErrors[] = \sprintf('Child process error: %s', $stdErrStreamBuffer->getBuffer());
            });
            $job = \array_pop($jobs);
            $processStdInEncoder->write([self::ACTION => \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::CHECK, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES => $job, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => \count($systemErrors)]);
        }
        $streamSelectLoop->run();
        if ($reachedSystemErrorsCountLimit) {
            $systemErrors[] = \sprintf('Reached system errors count limit of %d, exiting...', $this->systemErrorsCountLimit);
        }
        return [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS => $codingStandardErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $fileDiffs, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => \count($systemErrors)];
    }
}
