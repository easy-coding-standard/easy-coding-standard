<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\Skipper;

use SplFileInfo;
use Symplify\EasyCodingStandard\Skipper\Matcher\FileInfoMatcher;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\SkipSkipperTest
 */
final readonly class SkipSkipper
{
    public function __construct(
        private FileInfoMatcher $fileInfoMatcher
    ) {
    }

    /**
     * @param array<string, string[]|null> $skippedClasses
     */
    public function doesMatchSkip(object | string $checker, SplFileInfo | string $file, array $skippedClasses): bool
    {
        foreach ($skippedClasses as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            // skip everywhere
            if (! is_array($skippedFiles)) {
                return true;
            }

            if ($this->fileInfoMatcher->doesFileInfoMatchPatterns($file, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }
}
