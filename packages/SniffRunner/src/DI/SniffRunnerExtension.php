<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\EasyCodingStandard\Configuration\Option\CheckersOption;

final class SniffRunnerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        dump($this->getContainerBuilder()->parameters[CheckersOption::NAME]);
        die;

        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }
}
