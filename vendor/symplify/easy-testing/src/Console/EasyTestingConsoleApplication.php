<?php

namespace Symplify\EasyTesting\Console;

use ECSPrefix20210511\Symfony\Component\Console\Application;
use ECSPrefix20210511\Symfony\Component\Console\Command\Command;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
final class EasyTestingConsoleApplication extends \ECSPrefix20210511\Symfony\Component\Console\Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(\Symplify\PackageBuilder\Console\Command\CommandNaming $commandNaming, array $commands)
    {
        foreach ($commands as $command) {
            $commandName = $commandNaming->resolveFromCommand($command);
            $command->setName($commandName);
            $this->add($command);
        }
        parent::__construct('Easy Testing');
    }
}
