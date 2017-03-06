<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\EasyCodingStandard\Application\Command\RunCommand;

interface ApplicationInterface
{
    public function runCommand(RunCommand $command): void;
}
