<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\HttpKernel;

use ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class ConfigTransformerKernel extends \ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \ConfigTransformer20210601\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle(), new \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle()];
    }
}
