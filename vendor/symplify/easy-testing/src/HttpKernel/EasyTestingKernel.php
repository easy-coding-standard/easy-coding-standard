<?php

declare (strict_types=1);
namespace ECSPrefix20210928\Symplify\EasyTesting\HttpKernel;

use ECSPrefix20210928\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210928\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20210928\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration($loader) : void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
