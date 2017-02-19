<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\FixerRunner\Fixer\FixerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Runner\Runner;

final class Application implements ApplicationInterface
{
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var Runner
     */
    private $runner;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        Runner $runner,
        FixerFactory $fixerFactory,
        SourceFinder $sourceFinder,
        FileProcessor $fileProcessor
    ) {
        $this->runner = $runner;
        $this->fixerFactory = $fixerFactory;
        $this->sourceFinder = $sourceFinder;
        $this->fileProcessor = $fileProcessor;
    }

    public function runCommand(RunApplicationCommand $command) : void
    {
        $fixers = $this->fixerFactory->createFromClasses($command->getFixers());
        $this->runner->registerFixers($fixers);

        $this->runForSource($command->getSources(), $command->isFixer());
    }

    private function runForSource(array $source, bool $isFixer)
    {
        $files = $this->sourceFinder->find($source);
        $this->fileProcessor->processFiles($files, $isFixer);
    }
}
