<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symplify\EasyCodingStandard\Compiler\Exception\ShouldNotHappenException;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use Symplify\EasyCodingStandard\Yaml\FileLoader\CheckerTolerantYamlFileLoader;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;

/**
 * @see \Symplify\EasyCodingStandard\ChangedFilesDetector\Tests\FileHashComputerTest
 */
final class FileHashComputer
{
    /**
     * @var string
     */
    private const YAML_SUFFIX_PATTERN = '#\.(yml|yaml)$#';

    public function compute(string $filePath): string
    {
        if (! Strings::match($filePath, self::YAML_SUFFIX_PATTERN)) {
            $fileHash = md5_file($filePath);
            if (! $fileHash) {
                throw new FileNotFoundException(sprintf('File "%s" was not found', $fileHash));
            }

            return $fileHash;
        }

        $containerBuilder = new ContainerBuilder();

        $loader = $this->createLoader($filePath, $containerBuilder);
        $loader->load($filePath);

        return $this->arrayToHash($containerBuilder->getDefinitions()) .
            $this->arrayToHash($containerBuilder->getParameterBag()->all());
    }

    /**
     * @param mixed[] $array
     */
    private function arrayToHash(array $array): string
    {
        return md5(serialize($array));
    }

    private function createLoader(string $filePath, ContainerBuilder $containerBuilder): LoaderInterface
    {
        $fileLocator = new FileLocator([dirname($filePath)]);
        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($containerBuilder, $fileLocator),
            new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator),
            new CheckerTolerantYamlFileLoader($containerBuilder, $fileLocator),
        ]);

        $loader = $loaderResolver->resolve($filePath);
        if (! $loader) {
            throw new ShouldNotHappenException();
        }

        return $loader;
    }
}
