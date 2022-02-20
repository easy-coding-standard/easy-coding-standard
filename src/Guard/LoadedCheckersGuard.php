<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Guard;

use ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle;
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
    public function __construct(\Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector, \ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
        $this->symfonyStyle = $symfonyStyle;
    }
    public function areSomeCheckersRegistered() : bool
    {
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            if ($fileProcessor->getCheckers()) {
                return \true;
            }
        }
        return \false;
    }
    public function report() : void
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
}
