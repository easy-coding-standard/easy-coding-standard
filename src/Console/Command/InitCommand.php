<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class InitCommand extends Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly SymfonyStyle $symfonyStyle,
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
        $doesConfigExists = $this->filesystem->exists(getcwd() . '/ecs.php');

        // @todo figure out a better versoin
        if (! $doesConfigExists) {
            $this->filesystem->copy(__DIR__ . '/../../../templates/ecs.php.dist', getcwd() . '/ecs.php');
            $this->symfonyStyle->success('ecs.php config file has been generated successfully');
        } else {
            $this->symfonyStyle->warning('The "ecs.php" configuration file already exists');
        }

        return self::SUCCESS;
    }
}
