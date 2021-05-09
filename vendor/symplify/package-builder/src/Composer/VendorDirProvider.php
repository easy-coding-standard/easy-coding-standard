<?php

namespace Symplify\PackageBuilder\Composer;

use Composer\Autoload\ClassLoader;
use Nette\Utils\Strings;
use ReflectionClass;

/**
 * @see \Symplify\PackageBuilder\Tests\Composer\VendorDirProviderTest
 */
final class VendorDirProvider
{
    /**
     * @return string
     */
    public function provide()
    {
        $rootFolder = getenv('SystemDrive', true) . DIRECTORY_SEPARATOR;

        $path = __DIR__;
        while (! Strings::endsWith($path, 'vendor') && $path !== $rootFolder) {
            $path = dirname($path);
        }

        if ($path !== $rootFolder) {
            return $path;
        }

        return $this->reflectionFallback();
    }

    /**
     * @return string
     */
    private function reflectionFallback()
    {
        $reflectionClass = new ReflectionClass(ClassLoader::class);
        return dirname($reflectionClass->getFileName(), 2);
    }
}
