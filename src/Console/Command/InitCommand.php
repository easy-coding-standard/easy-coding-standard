<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202208\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202208\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202208\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class InitCommand extends AbstractSymplifyCommand
{
    protected function configure() : void
    {
        $this->setName('init');
        $this->setDescription('Generate ecs.php configuration file');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
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
