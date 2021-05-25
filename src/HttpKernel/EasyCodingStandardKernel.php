<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\HttpKernel;

use ECSPrefix20210525\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210525\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210525\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210525\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use ECSPrefix20210525\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\DeprecationWarningCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use ECSPrefix20210525\Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use ECSPrefix20210525\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20210525\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20210525\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends \ECSPrefix20210525\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        $bundles = [new \Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle(), new \Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle(), new \ECSPrefix20210525\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle(), new \ECSPrefix20210525\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle(), new \ECSPrefix20210525\Symplify\Skipper\Bundle\SkipperBundle()];
        if ($this->environment === 'test') {
            $bundles[] = new \ECSPrefix20210525\Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle();
        }
        return $bundles;
    }
    /**
     * @return void
     */
    protected function build(\ECSPrefix20210525\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\DeprecationWarningCompilerPass());
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(\ECSPrefix20210525\Symfony\Component\DependencyInjection\ContainerInterface $container) : \ECSPrefix20210525\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $delegatingLoaderFactory = new \Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
