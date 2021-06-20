<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210620\Clue\React\NDJson\Decoder;
use ECSPrefix20210620\Clue\React\NDJson\Encoder;
use ECSPrefix20210620\React\EventLoop\StreamSelectLoop;
use ECSPrefix20210620\React\Stream\ReadableResourceStream;
use ECSPrefix20210620\React\Stream\WritableResourceStream;
use ECSPrefix20210620\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210620\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20210620\Symplify\PackageBuilder\Console\ShellCode;
use ECSPrefix20210620\Symplify\SmartFileSystem\SmartFileInfo;
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
        parent::configure();
        $this->setDescription('(Internal) Support for parallel process');
    }
    protected function execute(\ECSPrefix20210620\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210620\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $configuration = $this->configurationFactory->createFromInput($input);
        $streamSelectLoop = new \ECSPrefix20210620\React\EventLoop\StreamSelectLoop();
        $stdOutEncoder = new \ECSPrefix20210620\Clue\React\NDJson\Encoder(new \ECSPrefix20210620\React\Stream\WritableResourceStream(\STDOUT, $streamSelectLoop));
        $handleErrorCallback = static function (\Throwable $throwable) use($stdOutEncoder) {
            $stdOutEncoder->write(['errors' => [$throwable->getMessage()], 'files_count' => 0, 'internal_errors_count' => 1]);
            $stdOutEncoder->end();
        };
        $stdOutEncoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
        // collectErrors from file processor
        $decoder = new \ECSPrefix20210620\Clue\React\NDJson\Decoder(new \ECSPrefix20210620\React\Stream\ReadableResourceStream(\STDIN, $streamSelectLoop), \true);
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) use($stdOutEncoder, $configuration) {
            $action = $json['action'];
            if ($action === \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::CHECK) {
                $internalErrorsCount = 0;
                $filePaths = $json['files'];
                $errorAndFileDiffs = [];
                $internalErrors = [];
                foreach ($filePaths as $filePath) {
                    try {
                        $smartFileInfo = new \ECSPrefix20210620\Symplify\SmartFileSystem\SmartFileInfo($filePath);
                        $currentErrorsAndFileDiffs = $this->singleFileProcessor->processFileInfo($smartFileInfo, $configuration);
                        $errorAndFileDiffs = \array_merge($errorAndFileDiffs, $currentErrorsAndFileDiffs);
                    } catch (\Throwable $throwable) {
                        ++$internalErrorsCount;
                        $errorMessage = \sprintf('Internal error: %s', $throwable->getMessage());
                        $errorMessage .= 'Run ECS with "--debug" option and post report here: https://github.com/symplify/symplify/issues/new';
                        $internalErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $errorMessage, $filePath);
                    }
                }
                $stdOutEncoder->write(['errors' => $errorAndFileDiffs, 'files_count' => \is_array($filePaths) || $filePaths instanceof \Countable ? \count($filePaths) : 0, 'system_errors' => $internalErrors, 'system_errors_count' => $internalErrorsCount]);
            } elseif ($action === \Symplify\EasyCodingStandard\Parallel\ValueObject\Action::QUIT) {
                $stdOutEncoder->end();
            }
        });
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
        $streamSelectLoop->run();
        return \ECSPrefix20210620\Symplify\PackageBuilder\Console\ShellCode::SUCCESS;
    }
}
