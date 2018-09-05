<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Autowire configs by default
 */
final class AutowireCheckersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (! $this->isCheckerDefinition($definition)) {
                continue;
            }
            $definition->setAutowired(true);
        }
    }

    private function isCheckerDefinition(Definition $definition): bool
    {
        if ($definition->getClass() === null) {
            return false;
        }

        if (is_a($definition->getClass(), FixerInterface::class, true)) {
            return true;
        }

        return is_a($definition->getClass(), Sniff::class, true);
    }
}
