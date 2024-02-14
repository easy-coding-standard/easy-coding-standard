<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Skipper\Matcher;

use SplFileInfo;
use Symplify\EasyCodingStandard\Skipper\FileSystem\FnMatchPathNormalizer;
use Symplify\EasyCodingStandard\Skipper\Fnmatcher;
final class FileInfoMatcher
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Skipper\FileSystem\FnMatchPathNormalizer
     */
    private $fnMatchPathNormalizer;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Skipper\Fnmatcher
     */
    private $fnmatcher;
    public function __construct(FnMatchPathNormalizer $fnMatchPathNormalizer, Fnmatcher $fnmatcher)
    {
        $this->fnMatchPathNormalizer = $fnMatchPathNormalizer;
        $this->fnmatcher = $fnmatcher;
    }
    /**
     * @param string[] $filePatterns
     * @param \SplFileInfo|string $fileInfo
     */
    public function doesFileInfoMatchPatterns($fileInfo, array $filePatterns) : bool
    {
        foreach ($filePatterns as $filePattern) {
            if ($this->doesFileInfoMatchPattern($fileInfo, $filePattern)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Supports both relative and absolute $file path. They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     * @param \SplFileInfo|string $file
     */
    private function doesFileInfoMatchPattern($file, string $ignoredPath) : bool
    {
        $filePath = $file instanceof SplFileInfo ? $file->getRealPath() : $file;
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
