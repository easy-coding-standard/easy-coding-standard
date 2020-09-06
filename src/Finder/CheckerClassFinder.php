<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Nette\Loaders\RobotLoader;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Finder\CheckerClassFinderTest
 */
final class CheckerClassFinder
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @param string[] $directories
     * @return class-string[]
     */
    public function findInDirectories(array $directories): array
    {
        $robotLoader = $this->createRobotLoaderForDirectories($directories);
        $checkerClasses = array_keys($robotLoader->getIndexedClasses());

        return $this->filterOutAbstractAndNonPhpClasses($checkerClasses);
    }

    /**
     * @param string[] $directories
     */
    private function createRobotLoaderForDirectories(array $directories): RobotLoader
    {
        $robot = new RobotLoader();
        $robot->setTempDirectory($this->createRobotLoaderCacheDirectory());
        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $robot->addDirectory($directory);
        }

        $robot->ignoreDirs = array_merge($robot->ignoreDirs, ['tests', 'Tests']);
        $robot->acceptFiles = ['*Sniff.php', '*Fixer.php'];
        $robot->rebuild();

        return $robot;
    }

    /**
     * @param string[] $checkerClasses
     * @return array<int, class-string>
     */
    private function filterOutAbstractAndNonPhpClasses(array $checkerClasses): array
    {
        $finalCheckerClasses = [];
        foreach ($checkerClasses as $checkerClass) {
            if (! class_exists($checkerClass)) {
                continue;
            }

            if ($this->isAbstractClass($checkerClass)) {
                continue;
            }

            if (is_a($checkerClass, Sniff::class, true) && ! $this->doesSniffSupportPhp($checkerClass)) {
                continue;
            }

            $finalCheckerClasses[] = $checkerClass;
        }

        return $finalCheckerClasses;
    }

    private function createRobotLoaderCacheDirectory(): string
    {
        $tempDir = sys_get_temp_dir() . '/_checker_finder_robot_loader';

        $this->smartFileSystem->mkdir($tempDir);

        return $tempDir;
    }

    private function isAbstractClass(string $class): bool
    {
        return (new ReflectionClass($class))->isAbstract();
    }

    private function doesSniffSupportPhp(string $sniffClass): bool
    {
        $vars = get_class_vars($sniffClass);
        if (! isset($vars['supportedTokenizers'])) {
            return true;
        }

        return in_array('PHP', $vars['supportedTokenizers'], true);
    }
}
