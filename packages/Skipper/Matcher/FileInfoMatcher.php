<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\Matcher;

use Symplify\EasyCodingStandard\Skipper\FileSystem\FnMatchPathNormalizer;
use Symplify\EasyCodingStandard\Skipper\Fnmatcher;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileInfoMatcher
{
    public function __construct(
        private FnMatchPathNormalizer $fnMatchPathNormalizer,
        private Fnmatcher $fnmatcher
    ) {
    }

    /**
     * @param string[] $filePatterns
     */
    public function doesFileInfoMatchPatterns(SmartFileInfo | string $smartFileInfo, array $filePatterns): bool
    {
        foreach ($filePatterns as $filePattern) {
            if ($this->doesFileInfoMatchPattern($smartFileInfo, $filePattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Supports both relative and absolute $file path. They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function doesFileInfoMatchPattern(SmartFileInfo | string $file, string $ignoredPath): bool
    {
        $filePath = $file instanceof SmartFileInfo ? $file->getRealPath() : $file;

        // in ecs.php, the path can be absolute
        if ($filePath === $ignoredPath) {
            return true;
        }

        $ignoredPath = $this->fnMatchPathNormalizer->normalizeForFnmatch($ignoredPath);
        if ($ignoredPath === '') {
            return false;
        }

        if (str_starts_with($filePath, $ignoredPath)) {
            return true;
        }

        if (str_ends_with($filePath, $ignoredPath)) {
            return true;
        }

        return $this->fnmatcher->match($ignoredPath, $filePath);
    }
}
