<?php

declare (strict_types=1);
namespace ECSPrefix20210624\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20210624\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210624\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20210624\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ECSPrefix20210624\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
