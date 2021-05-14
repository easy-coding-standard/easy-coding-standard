<?php

namespace ECSPrefix20210514\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20210514\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210514\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20210514\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ECSPrefix20210514\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
