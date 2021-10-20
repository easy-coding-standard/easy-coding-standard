<?php

declare (strict_types=1);
namespace ECSPrefix20211020\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20211020\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211020\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20211020\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration($loader) : void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
