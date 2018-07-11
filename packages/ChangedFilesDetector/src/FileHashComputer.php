<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class FileHashComputer
{
    public function compute(string $filePath): string
    {
        if (! Strings::match($filePath, '#\.(yml|yaml)$#')) {
            return md5_file($filePath);
        }

        $containerBuilder = new ContainerBuilder();

        $yamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator(dirname($filePath)));
        $yamlFileLoader->load($filePath);

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
}
