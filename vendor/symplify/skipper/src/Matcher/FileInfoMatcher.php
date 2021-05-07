<?php

namespace Symplify\Skipper\Matcher;

use Symplify\Skipper\FileSystem\PathNormalizer;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FileInfoMatcher
{
    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;
    /**
     * @param \Symplify\Skipper\FileSystem\PathNormalizer $pathNormalizer
     */
    public function __construct($pathNormalizer)
    {
        $this->pathNormalizer = $pathNormalizer;
    }
    /**
     * @param string[] $filePattern
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return bool
     */
    public function doesFileInfoMatchPatterns($smartFileInfo, array $filePattern)
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
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param string $ignoredPath
     * @return bool
     */
    private function doesFileInfoMatchPattern($smartFileInfo, $ignoredPath)
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
