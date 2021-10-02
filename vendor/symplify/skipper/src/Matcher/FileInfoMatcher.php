<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\Skipper\Matcher;

use ECSPrefix20211002\Symplify\Skipper\FileSystem\PathNormalizer;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
final class FileInfoMatcher
{
    /**
     * @var \Symplify\Skipper\FileSystem\PathNormalizer
     */
    private $pathNormalizer;
    public function __construct(\ECSPrefix20211002\Symplify\Skipper\FileSystem\PathNormalizer $pathNormalizer)
    {
        $this->pathNormalizer = $pathNormalizer;
    }
    /**
     * @param string[] $filePattern
     */
    public function doesFileInfoMatchPatterns(\ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, array $filePattern) : bool
    {
        foreach ($filePattern as $onlyFile) {
            if ($this->doesFileInfoMatchPattern($smartFileInfo, $onlyFile)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Supports both relative and absolute $file path. They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function doesFileInfoMatchPattern(\ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, string $ignoredPath) : bool
    {
        // in ecs.php, the path can be absolute
        if ($smartFileInfo->getRealPath() === $ignoredPath) {
            return \true;
        }
        $ignoredPath = $this->pathNormalizer->normalizeForFnmatch($ignoredPath);
        if ($ignoredPath === '') {
            return \false;
        }
        if ($smartFileInfo->startsWith($ignoredPath)) {
            return \true;
        }
        if ($smartFileInfo->endsWith($ignoredPath)) {
            return \true;
        }
        return $smartFileInfo->doesFnmatch($ignoredPath);
    }
}
