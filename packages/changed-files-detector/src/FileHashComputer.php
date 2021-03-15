<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\EasyCodingStandard\ChangedFilesDetector\Tests\FileHashComputer\FileHashComputerTest
 */
final class FileHashComputer
{
    public function computeConfig(string $filePath): string
    {
        $containerBuilder = new ContainerBuilder();

        $loader = $this->createLoader($filePath, $containerBuilder);
        $loader->load($filePath);

        $parameterBag = $containerBuilder->getParameterBag();
        return $this->arrayToHash($containerBuilder->getServiceIds()) . $this->arrayToHash($parameterBag->all());
    }

    public function compute(string $filePath): string
    {
        $fileHash = md5_file($filePath);
        if (! $fileHash) {
            throw new FileNotFoundException(sprintf('File "%s" was not found', $fileHash));
        }

        return $fileHash;
    }

    /**
     * @param mixed[] $array
     */
    private function arrayToHash(array $array): string
    {
        $serializedArray = serialize($array);
        return md5($serializedArray);
    }

    private function createLoader(string $filePath, ContainerBuilder $containerBuilder): LoaderInterface
    {
        $fileLocator = new FileLocator([dirname($filePath)]);
        $loaders = [
            new GlobFileLoader($containerBuilder, $fileLocator),
            new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator),
        ];
        $loaderResolver = new LoaderResolver($loaders);

        $loader = $loaderResolver->resolve($filePath);
        if (! $loader) {
            throw new ShouldNotHappenException();
        }

        return $loader;
    }
}
