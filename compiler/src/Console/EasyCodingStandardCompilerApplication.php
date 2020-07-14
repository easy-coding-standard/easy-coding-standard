<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Console;

use Symfony\Component\Console\Application;
use Symplify\EasyCodingStandard\Compiler\Command\CompileCommand;

final class EasyCodingStandardCompilerApplication extends Application
{
    public function __construct(CompileCommand $compileCommand)
    {
        parent::__construct('ecs.phar Compiler', 'v1.0');

        $this->add($compileCommand);
        $this->setDefaultCommand(CompileCommand::NAME, true);
    }
}
