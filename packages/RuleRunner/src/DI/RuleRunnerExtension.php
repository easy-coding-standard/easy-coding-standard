<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class RuleRunnerExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
        );
    }
}
