<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\Matcher;

use SplFileInfo;
use Symplify\EasyCodingStandard\Skipper\FileSystem\FnMatchPathNormalizer;
use Symplify\EasyCodingStandard\Skipper\Fnmatcher;

final class FileInfoMatcher
{
    public function __construct(
        private readonly FnMatchPathNormalizer $fnMatchPathNormalizer,
        private readonly Fnmatcher $fnmatcher
    ) {
    }

    /**
     * @param string[] $filePatterns
     */
    public function doesFileInfoMatchPatterns(SplFileInfo | string $fileInfo, array $filePatterns): bool
    {
        foreach ($filePatterns as $filePattern) {
            if ($this->doesFileInfoMatchPattern($fileInfo, $filePattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Supports both relative and absolute $file path. They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function doesFileInfoMatchPattern(SplFileInfo | string $file, string $ignoredPath): bool
    {
        $filePath = $file instanceof SplFileInfo ? $file->getRealPath() : $file;

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
