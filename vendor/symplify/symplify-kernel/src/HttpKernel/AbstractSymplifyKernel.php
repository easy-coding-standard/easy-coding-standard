<?php

namespace Symplify\SymplifyKernel\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\Strings\KernelUniqueHasher;

abstract class AbstractSymplifyKernel extends Kernel implements ExtraConfigAwareKernelInterface
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
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash();
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash() . '_log';
    }

    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new SymplifyKernelBundle()];
    }

    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs)
    {
        foreach ($configs as $config) {
            if ($config instanceof SmartFileInfo) {
                $config = $config->getRealPath();
            }

            $this->configs[] = $config;
        }
    }

    /**
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
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
        $kernelUniqueHasher = new KernelUniqueHasher();
        return $kernelUniqueHasher->hashKernelClass(static::class);
    }
}
