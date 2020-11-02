<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Bootstrap;

use Symfony\Component\Console\Style\SymfonyStyle;

final class NoCheckersLoaderReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function report(): void
    {
        $this->symfonyStyle->error('We could not find any sniffs/fixers rules to run');

        $this->symfonyStyle->writeln('You have few options to add them:');
        $this->symfonyStyle->newLine();

        $this->symfonyStyle->title('Add single rule to "ecs.php"');
        $this->symfonyStyle->writeln('  $services = $containerConfigurator->services();');
        $this->symfonyStyle->writeln('  $services->set(...);');
        $this->symfonyStyle->newLine(2);

        $this->symfonyStyle->title('Add set of rules to "ecs.php"');
        $this->symfonyStyle->writeln('  $parameters = $containerConfigurator->parameters();');
        $this->symfonyStyle->writeln('  $parameters->set(Option::SETS, [...]);');
        $this->symfonyStyle->newLine(2);

        $this->symfonyStyle->title('Missing "ecs.php" in your project? Let ECS create it for you');
        $this->symfonyStyle->writeln('  vendor/bin/ecs init');
        $this->symfonyStyle->newLine();
    }
}
