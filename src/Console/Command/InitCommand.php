<?php

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class InitCommand extends AbstractSymplifyCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Generate ecs.php configuration file');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
