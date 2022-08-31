<?php

declare (strict_types=1);
namespace ECSPrefix202208\Symplify\Skipper\Matcher;

use ECSPrefix202208\Symplify\Skipper\FileSystem\FnMatchPathNormalizer;
use ECSPrefix202208\Symplify\Skipper\Fnmatcher;
use ECSPrefix202208\Symplify\SmartFileSystem\SmartFileInfo;
final class FileInfoMatcher
{
    /**
     * @var \Symplify\Skipper\FileSystem\FnMatchPathNormalizer
     */
    private $fnMatchPathNormalizer;
    /**
     * @var \Symplify\Skipper\Fnmatcher
     */
    private $fnmatcher;
    public function __construct(FnMatchPathNormalizer $fnMatchPathNormalizer, Fnmatcher $fnmatcher)
    {
        $this->fnMatchPathNormalizer = $fnMatchPathNormalizer;
        $this->fnmatcher = $fnmatcher;
    }
    /**
     * @param string[] $filePatterns
     * @param \Symplify\SmartFileSystem\SmartFileInfo|string $smartFileInfo
     */
    public function doesFileInfoMatchPatterns($smartFileInfo, array $filePatterns) : bool
    {
        foreach ($filePatterns as $filePattern) {
            if ($this->doesFileInfoMatchPattern($smartFileInfo, $filePattern)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Supports both relative and absolute $file path. They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     * @param \Symplify\SmartFileSystem\SmartFileInfo|string $file
     */
    private function doesFileInfoMatchPattern($file, string $ignoredPath) : bool
    {
        $filePath = $file instanceof SmartFileInfo ? $file->getRealPath() : $file;
        // in ecs.php, the path can be absolute
        if ($filePath === $ignoredPath) {
            return \true;
        }
        $ignoredPath = $this->fnMatchPathNormalizer->normalizeForFnmatch($ignoredPath);
        if ($ignoredPath === '') {
            return \false;
        }
        if (\strncmp($filePath, $ignoredPath, \strlen($ignoredPath)) === 0) {
            return \true;
        }
        if (\substr_compare($filePath, $ignoredPath, -\strlen($ignoredPath)) === 0) {
            return \true;
        }
        return $this->fnmatcher->match($ignoredPath, $filePath);
    }
}
