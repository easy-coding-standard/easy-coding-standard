<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Guard;

use ECSPrefix202208\Symfony\Component\Console\Style\SymfonyStyle;
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
    public function __construct(FileProcessorCollector $fileProcessorCollector, SymfonyStyle $symfonyStyle)
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
        $this->symfonyStyle->writeln('  $ecsConfig->rule(...);');
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('Add set of rules to "ecs.php"');
        $this->symfonyStyle->writeln('  $ecsConfig->sets([...]);');
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('Missing "ecs.php" in your project? Let ECS create it for you');
        $this->symfonyStyle->writeln('  vendor/bin/ecs init');
        $this->symfonyStyle->newLine();
    }
}
