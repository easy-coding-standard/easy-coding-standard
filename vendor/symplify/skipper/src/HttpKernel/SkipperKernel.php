<?php

namespace Symplify\Skipper\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\Skipper\Bundle\SkipperBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SkipperKernel extends AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        parent::registerContainerConfiguration($loader);
    }

    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new SkipperBundle(), new SymplifyKernelBundle()];
    }
}
