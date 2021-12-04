<?php

declare (strict_types=1);
namespace ECSPrefix20211204\Symplify\SymplifyKernel\DependencyInjection;

use ECSPrefix20211204\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use ECSPrefix20211204\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Mimics @see \Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass without dependency on
 * symfony/http-kernel
 */
final class LoadExtensionConfigsCompilerPass extends \ECSPrefix20211204\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function process($containerBuilder) : void
    {
        $extensionNames = \array_keys($containerBuilder->getExtensions());
        foreach ($extensionNames as $extensionName) {
            $containerBuilder->loadFromExtension($extensionName, []);
        }
        parent::process($containerBuilder);
    }
}
