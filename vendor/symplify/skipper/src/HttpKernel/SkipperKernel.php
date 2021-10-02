<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\Skipper\HttpKernel;

use ECSPrefix20211002\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211002\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ECSPrefix20211002\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20211002\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20211002\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class SkipperKernel extends \ECSPrefix20211002\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration($loader) : void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        parent::registerContainerConfiguration($loader);
    }
    /**
     * @return BundleInterface[]
     */
    public function registerBundles() : iterable
    {
        return [new \ECSPrefix20211002\Symplify\Skipper\Bundle\SkipperBundle(), new \ECSPrefix20211002\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle()];
    }
}
