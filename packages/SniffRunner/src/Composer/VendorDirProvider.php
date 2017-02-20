<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Composer;

use Composer\Autoload\ClassLoader;
use ReflectionClass;

final class VendorDirProvider
{
    /**
     * @var string
     */
    private static $vendorDir;

    public static function provide(): string
    {
        if (self::$vendorDir) {
            return self::$vendorDir;
        }

        $classLoaderReflection = new ReflectionClass(ClassLoader::class);
        return self::$vendorDir = dirname(dirname($classLoaderReflection->getFileName()));
    }
}
