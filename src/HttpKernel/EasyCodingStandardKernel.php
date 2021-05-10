<?php

namespace Symplify\EasyCodingStandard\HttpKernel;

use ECSPrefix20210510\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210510\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\DeprecationWarningCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symplify\Skipper\Bundle\SkipperBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends \Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        $bundles = [new \Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle(), new \Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle(), new \Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle(), new \Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle(), new \Symplify\Skipper\Bundle\SkipperBundle()];
        if ($this->environment === 'test') {
            $bundles[] = new \Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle();
        }
        return $bundles;
    }
    /**
     * @return void
     */
    protected function build(\ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\DeprecationWarningCompilerPass());
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    protected function getContainerLoader(\ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $delegatingLoaderFactory = new \Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
