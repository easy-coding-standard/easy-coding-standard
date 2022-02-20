<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class InitCommand extends \ECSPrefix20220220\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    protected function configure() : void
    {
        $this->setName('init');
        $this->setDescription('Generate ecs.php configuration file');
    }
    protected function execute(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output) : int
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
