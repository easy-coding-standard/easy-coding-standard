<?php

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use ECSPrefix20210508\Symfony\Component\Config\FileLocator;
use ECSPrefix20210508\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210508\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\FileHashComputer\FileHashComputerTest
 */
final class FileHashComputer
{
    /**
     * @param string $filePath
     */
    public function computeConfig($filePath) : string
    {
        if (\is_object($filePath)) {
            $filePath = (string) $filePath;
        }
        $containerBuilder = new \ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder();
        $loader = $this->createLoader($filePath, $containerBuilder);
        $loader->load($filePath);
        $parameterBag = $containerBuilder->getParameterBag();
        return $this->arrayToHash($containerBuilder->getServiceIds()) . $this->arrayToHash($parameterBag->all());
    }
    /**
     * @param string $filePath
     */
    public function compute($filePath) : string
    {
        if (\is_object($filePath)) {
            $filePath = (string) $filePath;
        }
        $fileHash = \md5_file($filePath);
        if (!$fileHash) {
            throw new \Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException(\sprintf('File "%s" was not found', $fileHash));
        }
        return $fileHash;
    }
    /**
     * @param mixed[] $array
     * @return string
     */
    private function arrayToHash(array $array)
    {
        $serializedArray = \serialize($array);
        return \md5($serializedArray);
    }
    /**
     * @param string $filePath
     */
    private function createLoader($filePath, \ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : \ECSPrefix20210508\Symfony\Component\Config\Loader\LoaderInterface
    {
        if (\is_object($filePath)) {
            $filePath = (string) $filePath;
        }
        $fileLocator = new \ECSPrefix20210508\Symfony\Component\Config\FileLocator([\dirname($filePath)]);
        $loaders = [new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\GlobFileLoader($containerBuilder, $fileLocator), new \Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new \ECSPrefix20210508\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        $loader = $loaderResolver->resolve($filePath);
        if (!$loader) {
            throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $loader;
    }
}
