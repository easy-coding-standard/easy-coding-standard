<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\CheckersExtension;

final class FileHashComputer
{
    public function compute(string $filePath): string
    {
        if (Strings::endsWith($filePath, '.yml')) {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->registerExtension(new CheckersExtension());

            $yamlFileLoader = new YamlFileLoader($containerBuilder, new FileLocator(dirname($filePath)));
            $yamlFileLoader->load($filePath);

            return md5(serialize($containerBuilder));
        }

        return md5_file($filePath);
    }
}
