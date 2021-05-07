<?php

namespace Symplify\EasyCodingStandard\HttpKernel;

use ECSPrefix20210507\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210507\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\DeprecationWarningCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symplify\Skipper\Bundle\SkipperBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends AbstractSymplifyKernel
{
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        $bundles = [new EasyCodingStandardBundle(), new SymplifyCodingStandardBundle(), new ConsoleColorDiffBundle(), new SymplifyKernelBundle(), new SkipperBundle()];
        if ($this->environment === 'test') {
            $bundles[] = new PhpConfigPrinterBundle();
        }
        return $bundles;
    }
    /**
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    protected function build($containerBuilder)
    {
        $containerBuilder->addCompilerPass(new DeprecationWarningCompilerPass());
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    protected function getContainerLoader($container)
    {
        $delegatingLoaderFactory = new DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
