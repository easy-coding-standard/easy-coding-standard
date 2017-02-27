<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\SniffRunner\File\Provider\FilesProvider;
use Symplify\EasyCodingStandard\SniffRunner\Legacy\LegacyCompatibilityLayer;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory\SniffFactory;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\TokenDispatcher;

final class Application implements ApplicationInterface
{
    /**
     * @var TokenDispatcher
     */
    private $tokenDispatcher;

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
        TokenDispatcher $tokenDispatcher,
        FilesProvider $sourceFilesProvider,
        FileProcessor $fileProcessor,
        SniffFactory $sniffFactory
    ) {
        $this->tokenDispatcher = $tokenDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->fileProcessor = $fileProcessor;
        $this->sniffFactory = $sniffFactory;

        LegacyCompatibilityLayer::add();
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        $sniffs = $this->sniffFactory->createFromClasses($command->getSniffs());
        $this->tokenDispatcher->addSniffListeners($sniffs);

        $this->runForSource($command->getSources(), $command->isFixer());
    }

    /**
     * @param string[] $source
     * @param bool $isFixer
     */
    private function runForSource(array $source, bool $isFixer): void
    {
        $files = $this->filesProvider->getFilesForSource($source, $isFixer);
        $this->fileProcessor->processFiles($files, $isFixer);
    }
}
