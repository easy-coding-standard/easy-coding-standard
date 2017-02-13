<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\SniffDispatcher;
use Symplify\EasyCodingStandard\SniffRunner\File\Provider\FilesProvider;
use Symplify\EasyCodingStandard\SniffRunner\Legacy\LegacyCompatibilityLayer;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory\SniffFactory;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\SniffCollectionResolver;

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

    /**
     * @var SniffCollectionResolver
     */
    private $sniffCollectionResolver;

    public function __construct(
        SniffDispatcher $sniffDispatcher,
        FilesProvider $sourceFilesProvider,
        FileProcessor $fileProcessor,
        SniffFactory $sniffFactory,
        SniffCollectionResolver $sniffCollectionResolver
    ) {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->fileProcessor = $fileProcessor;
        $this->sniffFactory = $sniffFactory;
        $this->sniffCollectionResolver = $sniffCollectionResolver;

        LegacyCompatibilityLayer::add();
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        $sniffClasses = $this->sniffCollectionResolver->resolve(
            $command->getStandards(),
            $command->getSniffs(),
            $command->getExcludedSniffs()
        );

        $sniffs = $this->createSniffsFromSniffClasses($sniffClasses);
        $this->registerSniffsToSniffDispatcher($sniffs);

        $this->runForSource($command->getSources(), $command->isFixer());
    }

    /**
     * @return Sniff[]
     */
    private function createSniffsFromSniffClasses(array $sniffClasses): array
    {
        $sniffs = array();
        foreach ($sniffClasses as $sniffClass) {
            $sniffs[] = $this->sniffFactory->create($sniffClass);
        }

        return $sniffs;
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
