<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\EasyCodingStandard\Configuration\Option\SniffsOption;

final class SniffRunnerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );

        // @todo
        $sniffs = $this->getContainerBuilder()->parameters[SniffsOption::NAME];
        dump($sniffs);
        die;

        # 1.
        // sniff that way
        // addDefinition()

        /// fixer
        // add method->configure

        // sniff
        // add addsetup ($... = $value)

        # drop sniff factory then ;)

        # 2.
        // collect them in beforeCompile in SniffRunnerExtension
    }
}
