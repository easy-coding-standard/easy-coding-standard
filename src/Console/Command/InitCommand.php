<?php

namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
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
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute($input, $output)
    {
        $rectorConfigFiles = $this->smartFileSystem->exists(\getcwd() . '/ecs.php');
        if (!$rectorConfigFiles) {
            $this->smartFileSystem->copy(__DIR__ . '/../../../ecs.php.dist', \getcwd() . '/ecs.php');
            $this->symfonyStyle->success('ecs.php config file has been generated successfully');
        } else {
            $this->symfonyStyle->warning('The "ecs.php" configuration file already exists');
        }
        return ShellCode::SUCCESS;
    }
}
