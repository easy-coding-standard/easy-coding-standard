<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Bundle;

use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\Bundle;
use ConfigTransformer20210601\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\DependencyInjection\Extension\PhpConfigPrinterExtension;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy\DummySymfonyVersionFeatureGuard;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy\DummyYamlFileContentProvider;
/**
 * This class is dislocated in non-standard location, so it's not added by symfony/flex to bundles.php and cause app to
 * crash. See https://github.com/symplify/symplify/issues/1952#issuecomment-628765364
 */
final class PhpConfigPrinterBundle extends \ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return void
     */
    public function build(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $this->registerDefaultImplementations($containerBuilder);
        $containerBuilder->addCompilerPass(new \ConfigTransformer20210601\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass());
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ConfigTransformer20210601\Symplify\PhpConfigPrinter\DependencyInjection\Extension\PhpConfigPrinterExtension();
    }
    /**
     * @return void
     */
    private function registerDefaultImplementations(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        // set default implementations, if none provided - for better developer experience out of the box
        if (!$containerBuilder->has(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface::class)) {
            $containerBuilder->autowire(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy\DummyYamlFileContentProvider::class)->setPublic(\true);
            $containerBuilder->setAlias(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface::class, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy\DummyYamlFileContentProvider::class);
        }
        if (!$containerBuilder->has(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface::class)) {
            $containerBuilder->autowire(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy\DummySymfonyVersionFeatureGuard::class)->setPublic(\true);
            $containerBuilder->setAlias(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface::class, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy\DummySymfonyVersionFeatureGuard::class);
        }
    }
}
