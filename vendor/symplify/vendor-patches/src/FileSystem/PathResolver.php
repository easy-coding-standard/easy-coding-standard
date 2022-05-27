<?php

declare (strict_types=1);
namespace ECSPrefix20220527\Symplify\VendorPatches\FileSystem;

use ECSPrefix20220527\Nette\Utils\Strings;
use ECSPrefix20220527\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220527\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class PathResolver
{
    /**
     * @see https://regex101.com/r/KhzCSu/1
     * @var string
     */
    private const VENDOR_PACKAGE_DIRECTORY_REGEX = '#^(?<vendor_package_directory>.*?vendor\\/(\\w|\\.|\\-)+\\/(\\w|\\.|\\-)+)\\/#si';
    public function resolveVendorDirectory(\ECSPrefix20220527\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : string
    {
        $match = \ECSPrefix20220527\Nette\Utils\Strings::match($fileInfo->getRealPath(), self::VENDOR_PACKAGE_DIRECTORY_REGEX);
        if (!isset($match['vendor_package_directory'])) {
            throw new \ECSPrefix20220527\Symplify\SymplifyKernel\Exception\ShouldNotHappenException('Could not resolve vendor package directory');
        }
        return $match['vendor_package_directory'];
    }
}
