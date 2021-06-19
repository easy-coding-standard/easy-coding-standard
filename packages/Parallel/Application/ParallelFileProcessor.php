<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use ECSPrefix20210619\Clue\React\NDJson\Decoder;
use ECSPrefix20210619\Clue\React\NDJson\Encoder;
use ECSPrefix20210619\React\ChildProcess\Process;
use ECSPrefix20210619\React\EventLoop\StreamSelectLoop;
use ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface;
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
    const ANALYSE = 'analyse';
    /**
     * @var string[]
     */
    const OPTIONS = ['paths', 'autoload-file', 'xdebug'];
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @param Closure(int):void|null $postFileCallback
     * @return array{errors: (string[]|\PHPStan\Analyser\Error[])}
     * @param string|null $projectConfigFile
     */
    public function analyse(\Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule $schedule, string $mainScript, bool $onlyFiles, $postFileCallback, $projectConfigFile, \ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : array
    {
        $internalErrorsCountLimit = $this->parameterProvider->provideIntParameter(\Symplify\EasyCodingStandard\ValueObject\Option::INTERNAL_ERROR_COUNT_LIMIT);
        $jobs = \array_reverse($schedule->getJobs());
        $streamSelectLoop = new \ECSPrefix20210619\React\EventLoop\StreamSelectLoop();
        $processes = [];
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        $errors = [];
        $internalErrors = [];
        $hasInferrablePropertyTypesFromConstructor = \false;
        $command = $this->getWorkerCommand($mainScript, $projectConfigFile, $input);
        $internalErrorsCount = 0;
        $reachedInternalErrorsCountLimit = \false;
        $handleError = static function (\Throwable $error) use($streamSelectLoop, &$internalErrors, &$internalErrorsCount, &$reachedInternalErrorsCountLimit) {
            $streamSelectLoop = null;
            $internalErrors[] = 'Internal error: ' . $error->getMessage();
            ++$internalErrorsCount;
            $reachedInternalErrorsCountLimit = \true;
            $streamSelectLoop->stop();
        };
        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            if ($jobs === []) {
                break;
            }
            $childProcess = new \ECSPrefix20210619\React\ChildProcess\Process($command);
            $childProcess->start($streamSelectLoop);
            $processStdInEncoder = new \ECSPrefix20210619\Clue\React\NDJson\Encoder($childProcess->stdin);
            $processStdInEncoder->on('error', $handleError);
            $processStdOutDecoder = new \ECSPrefix20210619\Clue\React\NDJson\Decoder($childProcess->stdout, \true, 512, 0, 4 * 1024 * 1024);
            $processStdOutDecoder->on('data', function (array $json) use($childProcess, &$internalErrors, &$errors, &$jobs, $processStdInEncoder, $postFileCallback, &$hasInferrablePropertyTypesFromConstructor, &$internalErrorsCount, &$reachedInternalErrorsCountLimit, $streamSelectLoop) {
                $internalErrorsCountLimit = null;
                $streamSelectLoop = null;
                foreach ($json['errors'] as $jsonError) {
                    if (\is_string($jsonError)) {
                        $internalErrors[] = \sprintf('Internal error: %s', $jsonError);
                        continue;
                    }
                    $errors[] = \Symplify\EasyCodingStandard\Parallel\Application\Error::decode($jsonError);
                }
                if ($postFileCallback !== null) {
                    $postFileCallback($json['filesCount']);
                }
                $hasInferrablePropertyTypesFromConstructor = $hasInferrablePropertyTypesFromConstructor || $json['hasInferrablePropertyTypesFromConstructor'];
                $internalErrorsCount += $json['internalErrorsCount'];
                if ($internalErrorsCount >= $internalErrorsCountLimit) {
                    $reachedInternalErrorsCountLimit = \true;
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
                $processStdInEncoder->write([self::ACTION => self::ANALYSE, 'files' => $job]);
            });
            $processStdOutDecoder->on('error', $handleError);
            $stdErrStreamBuffer = new \Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer($childProcess->stderr);
            $childProcess->on('exit', static function ($exitCode) use(&$internalErrors, $stdErrStreamBuffer) {
                if ($exitCode === 0) {
                    return;
                }
                $internalErrors[] = \sprintf('Child process error: %s', $stdErrStreamBuffer->getBuffer());
            });
            $job = \array_pop($jobs);
            $processStdInEncoder->write([self::ACTION => self::ANALYSE, 'files' => $job]);
            $processes[] = $childProcess;
        }
        $streamSelectLoop->run();
        if ($reachedInternalErrorsCountLimit) {
            $internalErrors[] = \sprintf('Reached internal errors count limit of %d, exiting...', $internalErrorsCountLimit);
        }
        return ['errors' => \array_merge($ignoredErrorHelperResult->process($errors, $onlyFiles, $reachedInternalErrorsCountLimit), $internalErrors, $ignoredErrorHelperResult->getWarnings()), 'hasInferrablePropertyTypesFromConstructor' => $hasInferrablePropertyTypesFromConstructor];
    }
    /**
     * @param string|null $projectConfigFile
     */
    private function getWorkerCommand(string $mainScript, $projectConfigFile, \ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : string
    {
        $args = \array_merge([\PHP_BINARY, $mainScript], \array_slice($_SERVER['argv'], 1));
        $processCommandArray = [];
        foreach ($args as $arg) {
            if (\in_array($arg, [self::ANALYSE, 'analyze'], \true)) {
                break;
            }
            $processCommandArray[] = \escapeshellarg($arg);
        }
        $processCommandArray[] = 'worker';
        if ($projectConfigFile !== null) {
            $processCommandArray[] = '--configuration';
            $processCommandArray[] = \escapeshellarg($projectConfigFile);
        }
        foreach (self::OPTIONS as $optionName) {
            /** @var bool|string|null $optionValue */
            $optionValue = $input->getOption($optionName);
            if (\is_bool($optionValue)) {
                if ($optionValue) {
                    $processCommandArray[] = \sprintf('--%s', $optionName);
                }
                continue;
            }
            if ($optionValue === null) {
                continue;
            }
            $processCommandArray[] = \sprintf('--%s', $optionName);
            $processCommandArray[] = \escapeshellarg($optionValue);
        }
        /** @var string[] $paths */
        $paths = $input->getArgument('paths');
        foreach ($paths as $path) {
            $processCommandArray[] = \escapeshellarg($path);
        }
        return \implode(' ', $processCommandArray);
    }
}
