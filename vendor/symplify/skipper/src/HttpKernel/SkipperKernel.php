<?php

declare (strict_types=1);
namespace ECSPrefix20210715\Symplify\Skipper\HttpKernel;

use ECSPrefix20210715\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210715\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ECSPrefix20210715\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20210715\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20210715\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class SkipperKernel extends \ECSPrefix20210715\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     * @return void
     */
    public function registerContainerConfiguration($loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        parent::registerContainerConfiguration($loader);
    }
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \ECSPrefix20210715\Symplify\Skipper\Bundle\SkipperBundle(), new \ECSPrefix20210715\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle()];
    }
}
