<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210619\Clue\React\NDJson\Decoder;
use ECSPrefix20210619\Clue\React\NDJson\Encoder;
use ECSPrefix20210619\React\EventLoop\StreamSelectLoop;
use ECSPrefix20210619\React\Stream\ReadableResourceStream;
use ECSPrefix20210619\React\Stream\WritableResourceStream;
use ECSPrefix20210619\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210619\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210619\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;
/**
 * Inspired at https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 */
final class WorkerCommand extends \ECSPrefix20210619\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Application\SingleFileProcessor
     */
    private $singleFileProcessor;
    public function __construct(\Symplify\EasyCodingStandard\Application\SingleFileProcessor $singleFileProcessor)
    {
        $this->singleFileProcessor = $singleFileProcessor;
        parent::__construct();
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('(Internal) Support for parallel process');
        $this->addArgument(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS, \ECSPrefix20210619\Symfony\Component\Console\Input\InputArgument::OPTIONAL | \ECSPrefix20210619\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Paths with source code to run analysis on');
    }
    protected function execute(\ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210619\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $singleFileProcessor = $this->singleFileProcessor;
        $streamSelectLoop = new \ECSPrefix20210619\React\EventLoop\StreamSelectLoop();
        $stdOutEncoder = new \ECSPrefix20210619\Clue\React\NDJson\Encoder(new \ECSPrefix20210619\React\Stream\WritableResourceStream(\STDOUT, $streamSelectLoop));
        $handleError = static function (\Throwable $error) use($stdOutEncoder) {
            $stdOutEncoder->write(['errors' => [$error->getMessage()], 'filesCount' => 0, 'internalErrorsCount' => 1]);
            $stdOutEncoder->end();
        };
        $stdOutEncoder->on('error', $handleError);
        // todo collectErrors (from Analyser)
        $decoder = new \ECSPrefix20210619\Clue\React\NDJson\Decoder(new \ECSPrefix20210619\React\Stream\ReadableResourceStream(\STDIN, $streamSelectLoop), \true);
        $decoder->on('data', static function (array $json) use($singleFileProcessor, $stdOutEncoder) {
            $inferrablePropertyTypesFromConstructorHelper = null;
            $action = $json['action'];
            if ($action === 'analyse') {
                $internalErrorsCount = 0;
                $filePaths = $json['files'];
                $errors = [];
                foreach ($filePaths as $filePath) {
                    try {
                        $singleFileProcessor->processFileInfo(new \ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileInfo($filePath));
                        $fileErrors = $fileAnalyser->analyseFile($filePath);
                        foreach ($fileErrors as $fileError) {
                            $errors[] = $fileError;
                        }
                    } catch (\Throwable $throwable) {
                        ++$internalErrorsCount;
                        $internalErrorMessage = \sprintf('Internal error: %s', $throwable->getMessage());
                        $internalErrorMessage .= 'Run ECS with --debug option';
                        $errors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $internalErrorMessage, $filePath);
                    }
                }
                $stdOutEncoder->write(['errors' => $errors, 'filesCount' => \is_array($filePaths) || $filePaths instanceof \Countable ? \count($filePaths) : 0, 'hasInferrablePropertyTypesFromConstructor' => $inferrablePropertyTypesFromConstructorHelper->hasInferrablePropertyTypesFromConstructor(), 'internalErrorsCount' => $internalErrorsCount]);
            } elseif ($action === 'quit') {
                $stdOutEncoder->end();
            }
        });
        $decoder->on('error', $handleError);
        $streamSelectLoop->run();
        return 0;
    }
}
