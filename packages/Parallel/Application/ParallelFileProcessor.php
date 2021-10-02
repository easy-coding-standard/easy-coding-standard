<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use ECSPrefix20211002\Clue\React\NDJson\Decoder;
use ECSPrefix20211002\Clue\React\NDJson\Encoder;
use ECSPrefix20211002\Nette\Utils\Random;
use ECSPrefix20211002\React\EventLoop\StreamSelectLoop;
use ECSPrefix20211002\React\Socket\ConnectionInterface;
use ECSPrefix20211002\React\Socket\TcpServer;
use ECSPrefix20211002\Symfony\Component\Console\Command\Command;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ParallelProcess;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ProcessPool;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20211002\Symplify\PackageBuilder\Parameter\ParameterProvider;
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
    public const TIMEOUT_IN_SECONDS = 60;
    /**
     * @var int
     */
    private $systemErrorsCountLimit;
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\ValueObject\ProcessPool|null
     */
    private $processPool = null;
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory
     */
    private $workerCommandLineFactory;
    public function __construct(\ECSPrefix20211002\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory $workerCommandLineFactory)
    {
        $this->workerCommandLineFactory = $workerCommandLineFactory;
        $this->systemErrorsCountLimit = $parameterProvider->provideIntParameter(\Symplify\EasyCodingStandard\ValueObject\Option::SYSTEM_ERROR_COUNT_LIMIT);
    }
    /**
     * @param Closure(int): void|null $postFileCallback Used for progress bar jump
     * @return mixed[]
     */
    public function check(\Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule $schedule, string $mainScript, \Closure $postFileCallback, ?string $projectConfigFile, \ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface $input) : array
    {
        $jobs = \array_reverse($schedule->getJobs());
        $streamSelectLoop = new \ECSPrefix20211002\React\EventLoop\StreamSelectLoop();
        // basic properties setup
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        // initial counters
        $codingStandardErrors = [];
        $fileDiffs = [];
        $systemErrors = [];
        // $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = \false;
        $tcpServer = new \ECSPrefix20211002\React\Socket\TcpServer('127.0.0.1:0', $streamSelectLoop);
        $this->processPool = new \Symplify\EasyCodingStandard\Parallel\ValueObject\ProcessPool($tcpServer);
        $tcpServer->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::CONNECTION, function (\ECSPrefix20211002\React\Socket\ConnectionInterface $connection) use(&$jobs) : void {
            $inDecoder = new \ECSPrefix20211002\Clue\React\NDJson\Decoder($connection, \true, 512, 0, 4 * 1024 * 1024);
            $outEncoder = new \ECSPrefix20211002\Clue\React\NDJson\Encoder($connection);
            $inDecoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $data) use(&$jobs, $inDecoder, $outEncoder) : void {
                $action = $data[\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION];
                if ($action !== \Symplify\EasyCodingStandard\Parallel\Enum\Action::HELLO) {
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
                $parallelProcess->request([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => \Symplify\EasyCodingStandard\Parallel\Enum\Action::CHECK, 'files' => $job]);
            });
        });
        /** @var string $serverAddress */
        $serverAddress = $tcpServer->getAddress();
        /** @var int $serverPort */
        $serverPort = \parse_url($serverAddress, \PHP_URL_PORT);
        $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = \false;
        $handleErrorCallable = static function (\Throwable $throwable) use($streamSelectLoop, &$systemErrors, &$systemErrorsCount, &$reachedSystemErrorsCountLimit) : void {
            $systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            ++$systemErrorsCount;
            $reachedSystemErrorsCountLimit = \true;
            $this->processPool->quitAll();
        };
        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            // nothing else to process, stop now
            if ($jobs === []) {
                break;
            }
            $processIdentifier = \ECSPrefix20211002\Nette\Utils\Random::generate();
            $workerCommandLine = $this->workerCommandLineFactory->create($mainScript, $projectConfigFile, $input, $processIdentifier, $serverPort);
            $parallelProcess = new \Symplify\EasyCodingStandard\Parallel\ValueObject\ParallelProcess($workerCommandLine, $streamSelectLoop, self::TIMEOUT_IN_SECONDS);
            $parallelProcess->start(
                // 1. callable on data
                function (array $json) use($parallelProcess, &$systemErrors, &$errors, &$fileDiffs, &$codingStandardErrors, &$jobs, $postFileCallback, &$systemErrorsCount, &$reachedInternalErrorsCountLimit, $processIdentifier) : void {
                    // decode arrays to objects
                    foreach ($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS] as $jsonError) {
                        if (\is_string($jsonError)) {
                            $systemErrors[] = \sprintf('System error: %s', $jsonError);
                            continue;
                        }
                        $errors[] = \Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError::decode($jsonError);
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
                    if ($systemErrorsCount >= $this->systemErrorsCountLimit) {
                        $reachedInternalErrorsCountLimit = \true;
                        $this->processPool->quitAll();
                    }
                    if ($jobs === []) {
                        $this->processPool->quitProcess($processIdentifier);
                        return;
                    }
                    $job = \array_pop($jobs);
                    $parallelProcess->request([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => \Symplify\EasyCodingStandard\Parallel\Enum\Action::CHECK, 'files' => $job]);
                },
                // 2. callable on error
                $handleErrorCallable,
                // 3. callable on exit
                function ($exitCode, string $stdErr) use(&$systemErrors, $processIdentifier) : void {
                    $this->processPool->tryQuitProcess($processIdentifier);
                    if ($exitCode === \ECSPrefix20211002\Symfony\Component\Console\Command\Command::SUCCESS) {
                        return;
                    }
                    if ($exitCode === null) {
                        return;
                    }
                    $systemErrors[] = \sprintf('Child process error: %s', $stdErr);
                }
            );
            $this->processPool->attachProcess($processIdentifier, $parallelProcess);
        }
        $streamSelectLoop->run();
        if ($reachedSystemErrorsCountLimit) {
            $systemErrors[] = \sprintf('Reached system errors count limit of %d, exiting...', $this->systemErrorsCountLimit);
        }
        return [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS => $codingStandardErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $fileDiffs, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => \count($systemErrors)];
    }
}
