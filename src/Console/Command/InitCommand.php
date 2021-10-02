<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class InitCommand extends \ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    protected function configure() : void
    {
        $this->setDescription('Generate ecs.php configuration file');
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output) : int
    {
        $rectorConfigFiles = $this->smartFileSystem->exists(\getcwd() . '/ecs.php');
        if (!$rectorConfigFiles) {
            $this->smartFileSystem->copy(__DIR__ . '/../../../ecs.php.dist', \getcwd() . '/ecs.php');
            $this->symfonyStyle->success('ecs.php config file has been generated successfully');
        } else {
            $this->symfonyStyle->warning('The "ecs.php" configuration file already exists');
        }
        return self::SUCCESS;
    }
}
