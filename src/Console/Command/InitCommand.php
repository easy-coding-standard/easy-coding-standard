<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class InitCommand extends AbstractSymplifyCommand
{
    protected function configure(): void
    {
        $this->setDescription('Generate rector.php configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rectorConfigFiles = $this->smartFileSystem->exists(getcwd() . '/ecs.php');

        if (! $rectorConfigFiles) {
            $this->smartFileSystem->copy(__DIR__ . '/../../../ecs.php.dist', getcwd() . '/ecs.php');
            $this->symfonyStyle->success('ecs.php config file has been generated successfully');
        } else {
            $this->symfonyStyle->warning('The "ecs.php" configuration file already exists');
        }

        return ShellCode::SUCCESS;
    }
}
