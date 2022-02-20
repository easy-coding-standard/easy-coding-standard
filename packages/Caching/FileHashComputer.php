<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix20220220\Symfony\Component\Config\FileLocator;
use ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use ECSPrefix20220220\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\FileHashComputer\FileHashComputerTest
 */
final class FileHashComputer
{
    public function computeConfig(string $filePath) : string
    {
        $containerBuilder = new \ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder();
        $loader = $this->createLoader($filePath, $containerBuilder);
        $loader->load($filePath);
        $parameterBag = $containerBuilder->getParameterBag();
        return $this->arrayToHash($containerBuilder->getServiceIds()) . $this->arrayToHash($parameterBag->all());
    }
    public function compute(string $filePath) : string
    {
        $fileHash = \md5_file($filePath);
        if (!$fileHash) {
            throw new \Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException(\sprintf('File "%s" was not found', $fileHash));
        }
        return $fileHash;
    }
    /**
     * @param mixed[] $array
     */
    private function arrayToHash(array $array) : string
    {
        $serializedArray = \serialize($array);
        return \md5($serializedArray);
    }
    private function createLoader(string $filePath, \ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : \ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new \ECSPrefix20220220\Symfony\Component\Config\FileLocator([\dirname($filePath)]);
        $loaders = [new \ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\GlobFileLoader($containerBuilder, $fileLocator), new \ECSPrefix20220220\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new \ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        $loader = $loaderResolver->resolve($filePath);
        if (!$loader) {
            throw new \ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $loader;
    }
}
