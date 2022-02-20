<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\Skipper\Skipper;

use ECSPrefix20220220\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\Skipper\Tests\Skipper\Skip\SkipSkipperTest
 */
final class SkipSkipper
{
    /**
     * @var \Symplify\Skipper\Matcher\FileInfoMatcher
     */
    private $fileInfoMatcher;
    public function __construct(\ECSPrefix20220220\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
    }
    /**
     * @param array<string, string[]|null> $skippedClasses
     * @param object|string $checker
     */
    public function doesMatchSkip($checker, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, array $skippedClasses) : bool
    {
        foreach ($skippedClasses as $skippedClass => $skippedFiles) {
            if (!\is_a($checker, $skippedClass, \true)) {
                continue;
            }
            // skip everywhere
            if (!\is_array($skippedFiles)) {
                return \true;
            }
            if ($this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedFiles)) {
                return \true;
            }
        }
        return \false;
    }
}
