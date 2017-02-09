<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    public function create() : Container
    {
        $configurator = new Configurator();
        $configurator->setDebugMode(true);
        $configurator->setTempDirectory($this->createAndReturnTempDir());
        $configurator->addConfig(__DIR__.'/../config/config.neon');

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir() : string
    {
        $tempDir = sys_get_temp_dir().'/symplify_multi_cs';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
