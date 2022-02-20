<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Application;

use ECSPrefix20220220\Clue\React\NDJson\Decoder;
use ECSPrefix20220220\Clue\React\NDJson\Encoder;
use ECSPrefix20220220\Nette\Utils\Random;
use ECSPrefix20220220\React\EventLoop\StreamSelectLoop;
use ECSPrefix20220220\React\Socket\ConnectionInterface;
use ECSPrefix20220220\React\Socket\TcpServer;
use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20220220\Symplify\EasyParallel\CommandLine\WorkerCommandLineFactory;
use ECSPrefix20220220\Symplify\EasyParallel\Enum\Action;
use ECSPrefix20220220\Symplify\EasyParallel\Enum\Content;
use ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand;
use ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactEvent;
use ECSPrefix20220220\Symplify\EasyParallel\ValueObject\ParallelProcess;
use ECSPrefix20220220\Symplify\EasyParallel\ValueObject\ProcessPool;
use ECSPrefix20220220\Symplify\EasyParallel\ValueObject\Schedule;
use ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider;
use Throwable;
/**
 * Inspired from @see
 * https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92#diff-39c7a3b0cbb217bbfff96fbb454e6e5e60c74cf92fbb0f9d246b8bebbaad2bb0
 *
 * https://github.com/phpstan/phpstan-src/commit/b84acd2e3eadf66189a64fdbc6dd18ff76323f67#diff-7f625777f1ce5384046df08abffd6c911cfbb1cfc8fcb2bdeaf78f337689e3e2R150
 */
final class ParallelFileProcessor
{
    /**
     * @var int
     */
    private const SYSTEM_ERROR_LIMIT = 50;
    /**
     * @var \Symplify\EasyParallel\ValueObject\ProcessPool|null
     */
    private $processPool = null;
    /**
     * @var \Symplify\EasyParallel\CommandLine\WorkerCommandLineFactory
     */
    private $workerCommandLineFactory;
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ECSPrefix20220220\Symplify\EasyParallel\CommandLine\WorkerCommandLineFactory $workerCommandLineFactory, \ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->workerCommandLineFactory = $workerCommandLineFactory;
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @param callable(int $stepCount): void $postFileCallback Used for progress bar jump
     * @return mixed[]
     */
    public function check(\ECSPrefix20220220\Symplify\EasyParallel\ValueObject\Schedule $schedule, string $mainScript, callable $postFileCallback, ?string $projectConfigFile, \ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input) : array
    {
        $jobs = \array_reverse($schedule->getJobs());
        $streamSelectLoop = new \ECSPrefix20220220\React\EventLoop\StreamSelectLoop();
        // basic properties setup
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        // initial counters
        $codingStandardErrors = [];
        $fileDiffs = [];
        $systemErrors = [];
        $tcpServer = new \ECSPrefix20220220\React\Socket\TcpServer('127.0.0.1:0', $streamSelectLoop);
        $this->processPool = new \ECSPrefix20220220\Symplify\EasyParallel\ValueObject\ProcessPool($tcpServer);
        $tcpServer->on(\ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactEvent::CONNECTION, function (\ECSPrefix20220220\React\Socket\ConnectionInterface $connection) use(&$jobs) : void {
            $inDecoder = new \ECSPrefix20220220\Clue\React\NDJson\Decoder($connection, \true, 512, 0, 4 * 1024 * 1024);
            $outEncoder = new \ECSPrefix20220220\Clue\React\NDJson\Encoder($connection);
            $inDecoder->on(\ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactEvent::DATA, function (array $data) use(&$jobs, $inDecoder, $outEncoder) : void {
                $action = $data[\ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand::ACTION];
                if ($action !== \ECSPrefix20220220\Symplify\EasyParallel\Enum\Action::HELLO) {
                    return;
                }
                $processIdentifier = $data[\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_IDENTIFIER];
                $parallelProcess = $this->processPool->getProcess($processIdentifier);
                $parallelProcess->bindConnection($inDecoder, $outEncoder);
                if ($jobs === []) {
                    $this->processPool->quitProcess($processIdentifier);
                    return;
                }
                $job = \array_pop($jobs);
                $parallelProcess->request([\ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand::ACTION => \ECSPrefix20220220\Symplify\EasyParallel\Enum\Action::MAIN, \ECSPrefix20220220\Symplify\EasyParallel\Enum\Content::FILES => $job]);
            });
        });
        /** @var string $serverAddress */
        $serverAddress = $tcpServer->getAddress();
        /** @var int $serverPort */
        $serverPort = \parse_url($serverAddress, \PHP_URL_PORT);
        $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = \false;
        $handleErrorCallable = function (\Throwable $throwable) use(&$systemErrors, &$systemErrorsCount, &$reachedSystemErrorsCountLimit) : void {
            $systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            ++$systemErrorsCount;
            $reachedSystemErrorsCountLimit = \true;
            $this->processPool->quitAll();
        };
        $timeoutInSeconds = $this->parameterProvider->provideIntParameter(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_TIMEOUT_IN_SECONDS);
        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            // nothing else to process, stop now
            if ($jobs === []) {
                break;
            }
            $processIdentifier = \ECSPrefix20220220\Nette\Utils\Random::generate();
            $workerCommandLine = $this->workerCommandLineFactory->create($mainScript, \Symplify\EasyCodingStandard\Console\Command\CheckCommand::class, 'worker', \Symplify\EasyCodingStandard\ValueObject\Option::PATHS, $projectConfigFile, $input, $processIdentifier, $serverPort);
            $parallelProcess = new \ECSPrefix20220220\Symplify\EasyParallel\ValueObject\ParallelProcess($workerCommandLine, $streamSelectLoop, $timeoutInSeconds);
            $parallelProcess->start(
                // 1. callable on data
                function (array $json) use($parallelProcess, &$systemErrors, &$fileDiffs, &$codingStandardErrors, &$jobs, $postFileCallback, &$systemErrorsCount, &$reachedInternalErrorsCountLimit, $processIdentifier) : void {
                    // decode arrays to objects
                    foreach ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS] as $jsonError) {
                        if (\is_string($jsonError)) {
                            $systemErrors[] = 'System error: ' . $jsonError;
                            continue;
                        }
                        $systemErrors[] = \Symplify\EasyCodingStandard\ValueObject\Error\SystemError::decode($jsonError);
                    }
                    foreach ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS] as $jsonError) {
                        $fileDiffs[] = \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff::decode($jsonError);
                    }
                    foreach ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] as $jsonError) {
                        $codingStandardErrors[] = \Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError::decode($jsonError);
                    }
                    // @todo why there is a null check?
                    if ($postFileCallback !== null) {
                        $postFileCallback($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT]);
                    }
                    $systemErrorsCount += $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT];
                    if ($systemErrorsCount >= self::SYSTEM_ERROR_LIMIT) {
                        $reachedInternalErrorsCountLimit = \true;
                        $this->processPool->quitAll();
                    }
                    if ($jobs === []) {
                        $this->processPool->quitProcess($processIdentifier);
                        return;
                    }
                    $job = \array_pop($jobs);
                    $parallelProcess->request([\ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand::ACTION => \ECSPrefix20220220\Symplify\EasyParallel\Enum\Action::MAIN, \ECSPrefix20220220\Symplify\EasyParallel\Enum\Content::FILES => $job]);
                },
                // 2. callable on error
                $handleErrorCallable,
                // 3. callable on exit
                function ($exitCode, string $stdErr) use(&$systemErrors, $processIdentifier) : void {
                    $this->processPool->tryQuitProcess($processIdentifier);
                    if ($exitCode === \ECSPrefix20220220\Symfony\Component\Console\Command\Command::SUCCESS) {
                        return;
                    }
                    if ($exitCode === null) {
                        return;
                    }
                    $systemErrors[] = 'Child process error: ' . $stdErr;
                }
            );
            $this->processPool->attachProcess($processIdentifier, $parallelProcess);
        }
        $streamSelectLoop->run();
        if ($reachedSystemErrorsCountLimit) {
            $systemErrors[] = \sprintf('Reached system errors count limit of %d, exiting...', self::SYSTEM_ERROR_LIMIT);
        }
        return [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS => $codingStandardErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $fileDiffs, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => \count($systemErrors)];
    }
}
