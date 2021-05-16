<?php

namespace ECSPrefix20210516\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20210516\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210516\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20210516\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ECSPrefix20210516\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
