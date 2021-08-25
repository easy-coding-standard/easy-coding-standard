<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210825\Clue\React\NDJson\Decoder;
use ECSPrefix20210825\Clue\React\NDJson\Encoder;
use ECSPrefix20210825\React\EventLoop\StreamSelectLoop;
use ECSPrefix20210825\React\Stream\ReadableResourceStream;
use ECSPrefix20210825\React\Stream\WritableResourceStream;
use ECSPrefix20210825\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210825\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20210825\Symplify\PackageBuilder\Yaml\ParametersMerger;
use ECSPrefix20210825\Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;
/**
 * Inspired at https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 */
final class WorkerCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Application\SingleFileProcessor
     */
    private $singleFileProcessor;
    /**
     * @var \Symplify\PackageBuilder\Yaml\ParametersMerger
     */
    private $parametersMerger;
    public function __construct(\Symplify\EasyCodingStandard\Application\SingleFileProcessor $singleFileProcessor, \ECSPrefix20210825\Symplify\PackageBuilder\Yaml\ParametersMerger $parametersMerger)
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
        $streamSelectLoop = new \ECSPrefix20210825\React\EventLoop\StreamSelectLoop();
        $stdOutEncoder = new \ECSPrefix20210825\Clue\React\NDJson\Encoder(new \ECSPrefix20210825\React\Stream\WritableResourceStream(\STDOUT, $streamSelectLoop));
        $handleErrorCallback = static function (\Throwable $throwable) use($stdOutEncoder) : void {
            $systemErrors = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            $stdOutEncoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => [$systemErrors], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT => 0, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => 1]);
            $stdOutEncoder->end();
        };
        $stdOutEncoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
        // collectErrors from file processor
        $decoder = new \ECSPrefix20210825\Clue\React\NDJson\Decoder(new \ECSPrefix20210825\React\Stream\ReadableResourceStream(\STDIN, $streamSelectLoop), \true);
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) use($stdOutEncoder, $configuration) : void {
            $action = $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION];
            if ($action === \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::CHECK) {
                $systemErrorsCount = 0;
                /** @var string[] $filePaths */
                $filePaths = $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES] ?? [];
                $errorAndFileDiffs = [];
                $systemErrors = [];
                foreach ($filePaths as $filePath) {
                    try {
                        $smartFileInfo = new \ECSPrefix20210825\Symplify\SmartFileSystem\SmartFileInfo($filePath);
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
                $stdOutEncoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS => $errorAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] ?? [], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $errorAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS] ?? [], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT => \count($filePaths), \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => $systemErrorsCount]);
            } elseif ($action === \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::QUIT) {
                $stdOutEncoder->end();
            }
        });
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
        $streamSelectLoop->run();
        return self::SUCCESS;
    }
}
