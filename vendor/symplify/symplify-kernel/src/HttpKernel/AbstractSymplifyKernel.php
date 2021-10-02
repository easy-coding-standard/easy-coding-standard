<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\SymplifyKernel\HttpKernel;

use ECSPrefix20211002\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211002\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ECSPrefix20211002\Symfony\Component\HttpKernel\Kernel;
use ECSPrefix20211002\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20211002\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20211002\Symplify\SymplifyKernel\Strings\KernelUniqueHasher;
abstract class AbstractSymplifyKernel extends \ECSPrefix20211002\Symfony\Component\HttpKernel\Kernel implements \ECSPrefix20211002\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface
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
     * @return BundleInterface[]
     */
    public function registerBundles() : iterable
    {
        return [new \ECSPrefix20211002\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle()];
    }
    /**
     * @param string[]|SmartFileInfo[] $configs
     */
    public function setConfigs($configs) : void
    {
        foreach ($configs as $config) {
            if ($config instanceof \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo) {
                $config = $config->getRealPath();
            }
            $this->configs[] = $config;
        }
    }
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration($loader) : void
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
    private function getUniqueKernelHash() : string
    {
        $kernelUniqueHasher = new \ECSPrefix20211002\Symplify\SymplifyKernel\Strings\KernelUniqueHasher();
        return $kernelUniqueHasher->hashKernelClass(static::class);
    }
}
