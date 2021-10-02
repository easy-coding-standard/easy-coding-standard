<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20211002\Clue\React\NDJson\Decoder;
use ECSPrefix20211002\Clue\React\NDJson\Encoder;
use ECSPrefix20211002\React\EventLoop\StreamSelectLoop;
use ECSPrefix20211002\React\Socket\ConnectionInterface;
use ECSPrefix20211002\React\Socket\TcpConnector;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20211002\Symplify\PackageBuilder\Yaml\ParametersMerger;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;
/**
 * Inspired at: https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 * https://github.com/phpstan/phpstan-src/blob/c471c7b050e0929daf432288770de673b394a983/src/Command/WorkerCommand.php
 *
 * ↓↓↓
 * https://github.com/phpstan/phpstan-src/commit/b84acd2e3eadf66189a64fdbc6dd18ff76323f67#diff-7f625777f1ce5384046df08abffd6c911cfbb1cfc8fcb2bdeaf78f337689e3e2
 */
final class WorkerCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var string
     */
    private const RESULT = 'result';
    /**
     * @var \Symplify\EasyCodingStandard\Application\SingleFileProcessor
     */
    private $singleFileProcessor;
    /**
     * @var \Symplify\PackageBuilder\Yaml\ParametersMerger
     */
    private $parametersMerger;
    public function __construct(\Symplify\EasyCodingStandard\Application\SingleFileProcessor $singleFileProcessor, \ECSPrefix20211002\Symplify\PackageBuilder\Yaml\ParametersMerger $parametersMerger)
    {
        $this->singleFileProcessor = $singleFileProcessor;
        $this->parametersMerger = $parametersMerger;
        parent::__construct();
    }
    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('(Internal) Support for parallel process');
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output) : int
    {
        $configuration = $this->configurationFactory->createFromInput($input);
        $streamSelectLoop = new \ECSPrefix20211002\React\EventLoop\StreamSelectLoop();
        $parallelIdentifier = $configuration->getParallelIdentifier();
        $tcpConnector = new \ECSPrefix20211002\React\Socket\TcpConnector($streamSelectLoop);
        $tcpConnector->connect(\sprintf('127.0.0.1:%d', $configuration->getParallelPort()))->then(function (\ECSPrefix20211002\React\Socket\ConnectionInterface $connection) use($output, $parallelIdentifier, $configuration) : void {
            $inDecoder = new \ECSPrefix20211002\Clue\React\NDJson\Decoder($connection, \true, 512, \JSON_INVALID_UTF8_IGNORE);
            $outEncoder = new \ECSPrefix20211002\Clue\React\NDJson\Encoder($connection, \JSON_INVALID_UTF8_IGNORE);
            // handshake?
            $outEncoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => \Symplify\EasyCodingStandard\Parallel\Enum\Action::HELLO, \Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::IDENTIFIER => $parallelIdentifier]);
            $this->runWorker($outEncoder, $inDecoder, $configuration);
        });
        $streamSelectLoop->run();
        return self::SUCCESS;
    }
    private function runWorker(\ECSPrefix20211002\Clue\React\NDJson\Encoder $encoder, \ECSPrefix20211002\Clue\React\NDJson\Decoder $decoder, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : void
    {
        // 1. handle system error
        $handleErrorCallback = static function (\Throwable $throwable) use($encoder) : void {
            $systemErrors = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            $encoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => self::RESULT, self::RESULT => [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => [$systemErrors], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT => 0, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => 1]]);
            $encoder->end();
        };
        $encoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
        // 2. collect diffs + errors from file processor
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) use($encoder, $configuration) : void {
            $action = $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION];
            if ($action !== \Symplify\EasyCodingStandard\Parallel\Enum\Action::CHECK) {
                return;
            }
            $systemErrorsCount = 0;
            /** @var string[] $filePaths */
            $filePaths = $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES] ?? [];
            $errorAndFileDiffs = [];
            $systemErrors = [];
            foreach ($filePaths as $filePath) {
                try {
                    $smartFileInfo = new \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo($filePath);
                    $currentErrorsAndFileDiffs = $this->singleFileProcessor->processFileInfo($smartFileInfo, $configuration);
                    $errorAndFileDiffs = $this->parametersMerger->merge($errorAndFileDiffs, $currentErrorsAndFileDiffs);
                } catch (\Throwable $throwable) {
                    ++$systemErrorsCount;
                    $errorMessage = \sprintf('System error: %s', $throwable->getMessage());
                    $errorMessage .= 'Run ECS with "--debug" option and post the report here: https://github.com/symplify/symplify/issues/new';
                    $systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $errorMessage, $filePath);
                }
            }
            /**
             * this invokes all listeners listening $decoder->on(...) @see ReactEvent::DATA
             */
            $encoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => self::RESULT, self::RESULT => [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS => $errorAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] ?? [], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $errorAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS] ?? [], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT => \count($filePaths), \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => $systemErrorsCount]]);
        });
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
    }
}
