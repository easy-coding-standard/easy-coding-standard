<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\EasyCodingStandard\Configuration\Option\CheckersOption;
use Symplify\EasyCodingStandard\Configuration\Option\FixersOption;
use Symplify\EasyCodingStandard\Configuration\Option\SniffsOption;

final class FixerRunnerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );

        // @todo
        $fixers = $this->getContainerBuilder()->parameters[FixersOption::NAME];
        dump($fixers);
        die;

        # 1.
        // fixer this way
        // addDefinition()

        /// fixer
        // add method->configure

        # drop fixer factory

        # 2.
        // collect them in beforeCompile
    }
}
