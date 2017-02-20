<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class FixerRunnerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }
}
