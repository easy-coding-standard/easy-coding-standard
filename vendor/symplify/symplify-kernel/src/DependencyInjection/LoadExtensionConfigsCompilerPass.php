<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\SymplifyKernel\DependencyInjection;

use ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Mimics @see \Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass without dependency on
 * symfony/http-kernel
 */
final class LoadExtensionConfigsCompilerPass extends \ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass
{
    public function process(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $extensionNames = \array_keys($containerBuilder->getExtensions());
        foreach ($extensionNames as $extensionName) {
            $containerBuilder->loadFromExtension($extensionName, []);
        }
        parent::process($containerBuilder);
    }
}
