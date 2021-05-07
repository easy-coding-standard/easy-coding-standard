<?php

namespace Symplify\EasyCodingStandard\Bootstrap;

use ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle;
final class NoCheckersLoaderReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     */
    public function __construct($symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
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
}
