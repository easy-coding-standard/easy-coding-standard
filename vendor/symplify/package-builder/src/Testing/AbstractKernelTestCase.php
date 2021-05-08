<?php

namespace Symplify\PackageBuilder\Testing;

use ECSPrefix20210508\PHPUnit\Framework\TestCase;
use ReflectionClass;
use ECSPrefix20210508\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210508\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Container;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210508\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\PackageBuilder\Exception\HttpKernel\MissingInterfaceException;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * Inspiration
 *
 * @see https://github.com/symfony/symfony/blob/master/src/Symfony/Bundle/FrameworkBundle/Test/KernelTestCase.php
 */
abstract class AbstractKernelTestCase extends \ECSPrefix20210508\PHPUnit\Framework\TestCase
{
    /**
     * @var KernelInterface
     */
    protected static $kernel;
    /**
     * @var ContainerInterface|Container
     */
    protected static $container;
    /**
     * @var array<string, KernelInterface>
     */
    private static $kernelsByHash = [];
    /**
     * @param class-string<KernelInterface> $kernelClass
     * @param string[]|SmartFileInfo[] $configs
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected function bootKernelWithConfigs($kernelClass, array $configs)
    {
        if (\is_object($kernelClass)) {
            $kernelClass = (string) $kernelClass;
        }
        // unwrap file infos to real paths
        $configFilePaths = $this->resolveConfigFilePaths($configs);
        $configsHash = $this->resolveConfigsHash($configFilePaths);
        $this->ensureKernelShutdown();
        $bootedKernel = $this->createBootedKernelFromConfigs($kernelClass, $configsHash, $configFilePaths);
        static::$kernel = $bootedKernel;
        return $bootedKernel;
    }
    /**
     * @param class-string<KernelInterface> $kernelClass
     * @param string[]|SmartFileInfo[] $configs
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected function bootKernelWithConfigsAndStaticCache($kernelClass, array $configs)
    {
        if (\is_object($kernelClass)) {
            $kernelClass = (string) $kernelClass;
        }
        // unwrap file infos to real paths
        $configFilePaths = $this->resolveConfigFilePaths($configs);
        $configsHash = $this->resolveConfigsHash($configFilePaths);
        if (isset(self::$kernelsByHash[$configsHash])) {
            static::$kernel = self::$kernelsByHash[$configsHash];
            self::$container = static::$kernel->getContainer();
        } else {
            $bootedKernel = $this->createBootedKernelFromConfigs($kernelClass, $configsHash, $configFilePaths);
            static::$kernel = $bootedKernel;
            self::$kernelsByHash[$configsHash] = $bootedKernel;
        }
        return static::$kernel;
    }
    /**
     * Syntax sugger to remove static from the test cases vission
     *
     * @template T of object
     * @param class-string<T> $type
     * @return object
     */
    protected function getService($type)
    {
        if (\is_object($type)) {
            $type = (string) $type;
        }
        if (self::$container === null) {
            throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException('First, crewate container with booKernel(KernelClass::class)');
        }
        return self::$container->get($type);
    }
    /**
     * @return void
     * @param string $kernelClass
     */
    protected function bootKernel($kernelClass)
    {
        if (\is_object($kernelClass)) {
            $kernelClass = (string) $kernelClass;
        }
        $this->ensureKernelShutdown();
        $kernel = new $kernelClass('test', \true);
        if (!$kernel instanceof \ECSPrefix20210508\Symfony\Component\HttpKernel\KernelInterface) {
            throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        static::$kernel = $this->bootAndReturnKernel($kernel);
    }
    /**
     * Shuts the kernel down if it was used in the test.
     * @return void
     */
    protected function ensureKernelShutdown()
    {
        if (static::$kernel !== null) {
            // make sure boot() is called
            // @see https://github.com/symfony/symfony/pull/31202/files
            $kernelReflectionClass = new \ReflectionClass(static::$kernel);
            $containerReflectionProperty = $kernelReflectionClass->getProperty('container');
            $containerReflectionProperty->setAccessible(\true);
            $kernel = $containerReflectionProperty->getValue(static::$kernel);
            if ($kernel !== null) {
                $container = static::$kernel->getContainer();
                static::$kernel->shutdown();
                if ($container instanceof \ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface) {
                    $container->reset();
                }
            }
        }
        static::$container = null;
    }
    /**
     * @param string[] $configs
     * @return string
     */
    protected function resolveConfigsHash(array $configs)
    {
        $configsHash = '';
        foreach ($configs as $config) {
            $configsHash .= \md5_file($config);
        }
        return \md5($configsHash);
    }
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return mixed[]
     */
    protected function resolveConfigFilePaths(array $configs)
    {
        $configFilePaths = [];
        foreach ($configs as $config) {
            $configFilePaths[] = $config instanceof \Symplify\SmartFileSystem\SmartFileInfo ? $config->getRealPath() : $config;
        }
        return $configFilePaths;
    }
    /**
     * @return void
     */
    private function ensureIsConfigAwareKernel(\ECSPrefix20210508\Symfony\Component\HttpKernel\KernelInterface $kernel)
    {
        if ($kernel instanceof \Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface) {
            return;
        }
        throw new \Symplify\PackageBuilder\Exception\HttpKernel\MissingInterfaceException(\sprintf('"%s" is missing an "%s" interface', \get_class($kernel), \Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface::class));
    }
    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    private function bootAndReturnKernel(\ECSPrefix20210508\Symfony\Component\HttpKernel\KernelInterface $kernel)
    {
        $kernel->boot();
        $container = $kernel->getContainer();
        // private → public service hack?
        if ($container->has('test.service_container')) {
            $container = $container->get('test.service_container');
        }
        if (!$container instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerInterface) {
            throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        // has output? keep it silent out of tests
        if ($container->has(\ECSPrefix20210508\Symfony\Component\Console\Style\SymfonyStyle::class)) {
            $symfonyStyle = $container->get(\ECSPrefix20210508\Symfony\Component\Console\Style\SymfonyStyle::class);
            $symfonyStyle->setVerbosity(\ECSPrefix20210508\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);
        }
        static::$container = $container;
        return $kernel;
    }
    /**
     * @param string[] $configFilePaths
     * @param string $kernelClass
     * @param string $configsHash
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    private function createBootedKernelFromConfigs($kernelClass, $configsHash, array $configFilePaths)
    {
        if (\is_object($configsHash)) {
            $configsHash = (string) $configsHash;
        }
        if (\is_object($kernelClass)) {
            $kernelClass = (string) $kernelClass;
        }
        $kernel = new $kernelClass('test_' . $configsHash, \true);
        $this->ensureIsConfigAwareKernel($kernel);
        /** @var ExtraConfigAwareKernelInterface $kernel */
        $kernel->setConfigs($configFilePaths);
        return $this->bootAndReturnKernel($kernel);
    }
}
