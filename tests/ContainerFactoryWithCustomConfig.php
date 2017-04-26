<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\PhpExtension;
use Nette\Utils\FileSystem;

final class ContainerFactoryWithCustomConfig
{
    public function createWithConfig(string $customConfig): Container
    {
        $configurator = new Configurator;
        $configurator->setDebugMode(true);
        $configurator->setTempDirectory($this->createAndReturnTempDir());

        $configurator->addConfig(__DIR__ . '/../src/config/config.neon');
        $configurator->addConfig($customConfig);

        $configurator->defaultExtensions = [
            'php' => PhpExtension::class,
            'extensions' => ExtensionsExtension::class,
            'cache' => [CacheExtension::class, ['%tempDir%']]
        ];

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir(): string
    {
        $tempDir = sys_get_temp_dir() . '/_' . sha1(self::class) . '_tests';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
