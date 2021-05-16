<?php

namespace ECSPrefix20210516\Symplify\Skipper\HttpKernel;

use ECSPrefix20210516\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210516\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ECSPrefix20210516\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20210516\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20210516\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class SkipperKernel extends \ECSPrefix20210516\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ECSPrefix20210516\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        parent::registerContainerConfiguration($loader);
    }
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \ECSPrefix20210516\Symplify\Skipper\Bundle\SkipperBundle(), new \ECSPrefix20210516\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle()];
    }
}
