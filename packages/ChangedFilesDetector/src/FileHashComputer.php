<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use Symplify\EasyCodingStandard\Yaml\FileLoader\CheckerTolerantYamlFileLoader;

final class FileHashComputer
{
    public function compute(string $filePath): string
    {
        if (! Strings::match($filePath, '#\.(yml|yaml)$#')) {
            $fileHash = md5_file($filePath);
            if ($fileHash === false) {
                throw new FileNotFoundException(sprintf('File "%s" was not found', $fileHash));
            }

            return $fileHash;
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
