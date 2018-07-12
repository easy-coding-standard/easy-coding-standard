<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Finder;

use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;

final class FixerClassRobotLoaderFactory
{
    public function createForDirectory(string $directory): RobotLoader
    {
        $robot = new RobotLoader();
        $robot->setTempDirectory($this->createCacheDirectory());
        $robot->addDirectory($directory);
        $robot->ignoreDirs += ['tests', 'Tests'];
        $robot->acceptFiles = ['*Fixer.php'];
        $robot->rebuild();

        return $robot;
    }

    private function createCacheDirectory(): string
    {
        $tempDir = sys_get_temp_dir() . '/sniff-runner-robot-loader';
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
