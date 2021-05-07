<?php

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Reference;
final class FixerWhitespaceConfigCompilerPass implements CompilerPassInterface
{
    /**
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function process($containerBuilder)
    {
        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $definition) {
            if ($definition->getClass() === null) {
                continue;
            }
            if (!\is_a($definition->getClass(), WhitespacesAwareFixerInterface::class, \true)) {
                continue;
            }
            $definition->addMethodCall('setWhitespacesConfig', [new Reference(WhitespacesFixerConfig::class)]);
        }
    }
}
