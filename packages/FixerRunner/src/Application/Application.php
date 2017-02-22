<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\FixerRunner\Fixer\FixerFactory;

final class Application implements ApplicationInterface
{
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        FixerFactory $fixerFactory,
        SourceFinder $sourceFinder,
        FileProcessor $fileProcessor
    ) {
        $this->fixerFactory = $fixerFactory;
        $this->sourceFinder = $sourceFinder;
        $this->fileProcessor = $fileProcessor;
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        $fixers = $this->fixerFactory->createFromClasses($command->getFixers());
        $this->fileProcessor->registerFixers($fixers);

        $this->runForSource($command->getSources(), $command->isFixer());
    }

    /**
     * @param string[] $source
     * @param bool $isFixer
     */
    private function runForSource(array $source, bool $isFixer): void
    {
        $files = $this->sourceFinder->find($source);
        $this->fileProcessor->processFiles($files, $isFixer);
    }
}
