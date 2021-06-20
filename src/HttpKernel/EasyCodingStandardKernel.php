<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\HttpKernel;

use ECSPrefix20210620\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210620\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210620\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210620\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use ECSPrefix20210620\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use ECSPrefix20210620\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20210620\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20210620\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends \ECSPrefix20210620\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle(), new \Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle(), new \ECSPrefix20210620\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle(), new \ECSPrefix20210620\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle(), new \ECSPrefix20210620\Symplify\Skipper\Bundle\SkipperBundle()];
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(\ECSPrefix20210620\Symfony\Component\DependencyInjection\ContainerInterface $container) : \ECSPrefix20210620\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $delegatingLoaderFactory = new \Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
