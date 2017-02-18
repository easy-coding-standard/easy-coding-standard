<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\SniffDispatcher;
use Symplify\EasyCodingStandard\SniffRunner\File\Provider\FilesProvider;
use Symplify\EasyCodingStandard\SniffRunner\Legacy\LegacyCompatibilityLayer;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory\SniffFactory;

final class Application implements ApplicationInterface
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    /**
     * @var FilesProvider
     */
    private $filesProvider;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var SniffFactory
     */
    private $sniffFactory;

    public function __construct(
        SniffDispatcher $sniffDispatcher,
        FilesProvider $sourceFilesProvider,
        FileProcessor $fileProcessor,
        SniffFactory $sniffFactory
    ) {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->fileProcessor = $fileProcessor;
        $this->sniffFactory = $sniffFactory;

        LegacyCompatibilityLayer::add();
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        $sniffs = $this->sniffFactory->createFromClasses($command->getSniffs());
        $this->registerSniffsToSniffDispatcher($sniffs);

        $this->runForSource($command->getSources(), $command->isFixer());
    }

    /**
     * @param Sniff[] $sniffs
     */
    private function registerSniffsToSniffDispatcher(array $sniffs): void
    {
        $this->sniffDispatcher->addSniffListeners($sniffs);
    }

    private function runForSource(array $source, bool $isFixer): void
    {
        $files = $this->filesProvider->getFilesForSource($source, $isFixer);
        $this->fileProcessor->processFiles($files, $isFixer);
    }
}
