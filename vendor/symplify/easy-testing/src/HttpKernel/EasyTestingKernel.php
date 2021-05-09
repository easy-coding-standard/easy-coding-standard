<?php

namespace Symplify\EasyTesting\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
