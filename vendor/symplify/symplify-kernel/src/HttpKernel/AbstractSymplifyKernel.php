<?php

namespace ECSPrefix20210515\Symplify\SymplifyKernel\HttpKernel;

use ECSPrefix20210515\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210515\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ECSPrefix20210515\Symfony\Component\HttpKernel\Kernel;
use ECSPrefix20210515\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210515\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20210515\Symplify\SymplifyKernel\Strings\KernelUniqueHasher;
abstract class AbstractSymplifyKernel extends \ECSPrefix20210515\Symfony\Component\HttpKernel\Kernel implements \ECSPrefix20210515\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];
    /**
     * @return string
     */
    public function getCacheDir()
    {
        return \sys_get_temp_dir() . '/' . $this->getUniqueKernelHash();
    }
    /**
     * @return string
     */
    public function getLogDir()
    {
        return \sys_get_temp_dir() . '/' . $this->getUniqueKernelHash() . '_log';
    }
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \ECSPrefix20210515\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle()];
    }
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs)
    {
        foreach ($configs as $config) {
            if ($config instanceof \ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo) {
                $config = $config->getRealPath();
            }
            $this->configs[] = $config;
        }
    }
    /**
     * @return void
     */
    public function registerContainerConfiguration(\ECSPrefix20210515\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
    /**
     * @return string
     */
    private function getUniqueKernelHash()
    {
        $kernelUniqueHasher = new \ECSPrefix20210515\Symplify\SymplifyKernel\Strings\KernelUniqueHasher();
        return $kernelUniqueHasher->hashKernelClass(static::class);
    }
}
