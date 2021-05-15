<?php

namespace ECSPrefix20210515\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20210515\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210515\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20210515\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ECSPrefix20210515\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
