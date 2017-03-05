<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder;

use Nette\Caching\Storages\FileStorage;
use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;

final class SniffClassRobotLoaderFactory
{
    public function createForDirectory(string $directory): RobotLoader
    {
        $robot = new RobotLoader;
        $robot->setCacheStorage($this->createCacheStorage());
        $robot->addDirectory($directory);
        $robot->ignoreDirs .= ', tests, Tests';
        $robot->acceptFiles = '*Sniff.php';
        $robot->rebuild();

        return $robot;
    }

    private function createCacheStorage(): FileStorage
    {
        $tempDir = sys_get_temp_dir() . '/sniff-runner-robot-loader';
        FileSystem::createDir($tempDir);

        return new FileStorage($tempDir);
    }
}
