<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigInitializer;

final class InitCommand extends Command
{
    public function __construct(
        private readonly ConfigInitializer $configInitializer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('init');
        $this->setDescription('Generate ecs.php configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // found some rules, we don't need more
        if ($this->configInitializer->areSomeCheckersRegistered()) {
            return self::SUCCESS;
        }

        $this->configInitializer->createConfig(getcwd());
        return self::SUCCESS;
    }
}
