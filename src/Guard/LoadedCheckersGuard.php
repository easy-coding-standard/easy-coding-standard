<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Guard;

use ECSPrefix20210618\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
final class LoadedCheckersGuard
{
    /**
     * @var \Symplify\EasyCodingStandard\Application\FileProcessorCollector
     */
    private $fileProcessorCollector;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(\Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector, \ECSPrefix20210618\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
        $this->symfonyStyle = $symfonyStyle;
    }
    public function areSomeCheckerRegistered() : bool
    {
        $checkerCount = $this->getCheckerCount();
        return $checkerCount !== 0;
    }
    /**
     * @return void
     */
    public function report()
    {
        $this->symfonyStyle->error('We could not find any sniffs/fixers rules to run');
        $this->symfonyStyle->writeln('You have few options to add them:');
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->title('Add single rule to "ecs.php"');
        $this->symfonyStyle->writeln('  $services = $containerConfigurator->services();');
        $this->symfonyStyle->writeln('  $services->set(...);');
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('Add set of rules to "ecs.php"');
        $this->symfonyStyle->writeln('  $containerConfigurator->import(...);');
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('Missing "ecs.php" in your project? Let ECS create it for you');
        $this->symfonyStyle->writeln('  vendor/bin/ecs init');
        $this->symfonyStyle->newLine();
    }
    private function getCheckerCount() : int
    {
        $checkerCount = 0;
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            $checkerCount += \count($fileProcessor->getCheckers());
        }
        return $checkerCount;
    }
}
