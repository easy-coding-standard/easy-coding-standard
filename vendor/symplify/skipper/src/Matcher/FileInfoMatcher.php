<?php

namespace ECSPrefix20210514\Symplify\Skipper\Matcher;

use ECSPrefix20210514\Symplify\Skipper\FileSystem\PathNormalizer;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
final class FileInfoMatcher
{
    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;
    public function __construct(\ECSPrefix20210514\Symplify\Skipper\FileSystem\PathNormalizer $pathNormalizer)
    {
        $this->pathNormalizer = $pathNormalizer;
    }
    /**
     * @param string[] $filePattern
     * @return bool
     */
    public function doesFileInfoMatchPatterns(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, array $filePattern)
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
     * @param string $ignoredPath
     * @return bool
     */
    private function doesFileInfoMatchPattern(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $ignoredPath)
    {
        $ignoredPath = (string) $ignoredPath;
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
