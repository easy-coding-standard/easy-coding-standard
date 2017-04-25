<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\PhpExtension;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    /**
     * @var string
     */
    private const CONFIG_NAME = 'easy-coding-standard.neon';

    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setDebugMode(true);
        $configurator->setTempDirectory($this->createAndReturnTempDir());

        $this->loadConfigFiles($configurator);

        $configurator->defaultExtensions = [
            'php' => PhpExtension::class,
            'extensions' => ExtensionsExtension::class,
            'cache' => [CacheExtension::class, ['%tempDir%']]
        ];

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir(): string
    {
        $tempDir = sys_get_temp_dir() . '/_' . sha1(self::class);
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }

    private function loadConfigFiles(Configurator $configurator): void
    {
        $configurator->addConfig(__DIR__ . '/../config/config.neon');
        $localConfig = getcwd() . '/' . self::CONFIG_NAME;
        if (file_exists($localConfig)) {
            $configurator->addConfig($localConfig);
        }
    }
}
