<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel;

use ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Kernel;
use ConfigTransformer20210601\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Strings\KernelUniqueHasher;
abstract class AbstractSymplifyKernel extends \ConfigTransformer20210601\Symfony\Component\HttpKernel\Kernel implements \ConfigTransformer20210601\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];
    public function getCacheDir() : string
    {
        return \sys_get_temp_dir() . '/' . $this->getUniqueKernelHash();
    }
    public function getLogDir() : string
    {
        return \sys_get_temp_dir() . '/' . $this->getUniqueKernelHash() . '_log';
    }
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \ConfigTransformer20210601\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle()];
    }
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs)
    {
        foreach ($configs as $config) {
            if ($config instanceof \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo) {
                $config = $config->getRealPath();
            }
            $this->configs[] = $config;
        }
    }
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
    private function getUniqueKernelHash() : string
    {
        $kernelUniqueHasher = new \ConfigTransformer20210601\Symplify\SymplifyKernel\Strings\KernelUniqueHasher();
        return $kernelUniqueHasher->hashKernelClass(static::class);
    }
}
