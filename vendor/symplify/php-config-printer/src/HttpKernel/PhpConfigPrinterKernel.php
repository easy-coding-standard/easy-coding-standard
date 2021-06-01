<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\HttpKernel;

use ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ConfigTransformer20210601\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class PhpConfigPrinterKernel extends \ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel implements \ConfigTransformer20210601\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle()];
    }
    /**
     * @param string[] $configs
     * @return void
     */
    public function setConfigs(array $configs)
    {
        $this->configs = $configs;
    }
}
