<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\EasyTesting\HttpKernel;

use ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface;
use ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
