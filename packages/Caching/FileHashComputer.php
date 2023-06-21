<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix202306\Symfony\Component\Config\FileLocator;
use ECSPrefix202306\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix202306\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix202306\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use Symplify\EasyCodingStandard\Exception\ShouldNotHappenException;
use ECSPrefix202306\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\FileHashComputer\FileHashComputerTest
 */
final class FileHashComputer
{
    public function computeConfig(string $filePath) : string
    {
        $containerBuilder = new ContainerBuilder();
        $loader = $this->createLoader($filePath, $containerBuilder);
        $loader->load($filePath);
        return $this->arrayToHash($containerBuilder->getServiceIds()) . SimpleParameterProvider::hash();
    }
    public function compute(string $filePath) : string
    {
        $fileHash = \md5_file($filePath);
        if (!$fileHash) {
            throw new FileNotFoundException(\sprintf('File "%s" was not found', $fileHash));
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
    private function createLoader(string $filePath, ContainerBuilder $containerBuilder) : LoaderInterface
    {
        $fileLocator = new FileLocator([\dirname($filePath)]);
        $loaders = [new GlobFileLoader($containerBuilder, $fileLocator), new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new LoaderResolver($loaders);
        $loader = $loaderResolver->resolve($filePath);
        if (!$loader) {
            throw new ShouldNotHappenException();
        }
        return $loader;
    }
}
