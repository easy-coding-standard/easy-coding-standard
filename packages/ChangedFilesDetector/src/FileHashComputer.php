<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Exception\FileHashFailedException;
use Symplify\EasyCodingStandard\Yaml\FileLoader\CheckerTolerantYamlFileLoader;
use function Safe\sprintf;

final class FileHashComputer
{
    public function compute(string $filePath): string
    {
        if (! Strings::match($filePath, '#\.(yml|yaml)$#')) {
            $md5File = md5_file($filePath);
            if ($md5File === false) {
                throw new FileHashFailedException(sprintf(
                    'Hashing of "%s" file for cache failed. Check the content, existance or access right to the file.',
                    $filePath
                ));
            }

            return $md5File;
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
