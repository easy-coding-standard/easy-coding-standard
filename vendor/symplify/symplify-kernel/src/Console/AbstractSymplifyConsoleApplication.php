<?php

namespace Symplify\SymplifyKernel\Console;

use ECSPrefix20210509\Symfony\Component\Console\Application;
use ECSPrefix20210509\Symfony\Component\Console\Command\Command;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
abstract class AbstractSymplifyConsoleApplication extends \ECSPrefix20210509\Symfony\Component\Console\Application
{
    /**
     * @var CommandNaming
     */
    private $commandNaming;
    /**
     * @param Command[] $commands
     * @param string $name
     * @param string $version
     */
    public function __construct(array $commands, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $name = (string) $name;
        $version = (string) $version;
        $this->commandNaming = new \Symplify\PackageBuilder\Console\Command\CommandNaming();
        $this->addCommands($commands);
        parent::__construct($name, $version);
    }
    /**
     * Add names to all commands by class-name convention
     *
     * @param Command[] $commands
     * @return void
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $command) {
            $commandName = $this->commandNaming->resolveFromCommand($command);
            $command->setName($commandName);
        }
        parent::addCommands($commands);
    }
}
