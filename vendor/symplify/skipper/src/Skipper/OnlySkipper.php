<?php

namespace Symplify\Skipper\Skipper;

use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\Skipper\Tests\Skipper\Only\OnlySkipperTest
 */
final class OnlySkipper
{
    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;

    public function __construct(FileInfoMatcher $fileInfoMatcher)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
    }

    /**
     * @param object|string $checker
     * @param mixed[] $only
     * @return bool|null
     */
    public function doesMatchOnly($checker, SmartFileInfo $smartFileInfo, array $only)
    {
        foreach ($only as $onlyClass => $onlyFiles) {
            if (is_int($onlyClass)) {
                // solely class
                $onlyClass = $onlyFiles;
                $onlyFiles = null;
            }

            if (! is_a($checker, $onlyClass, true)) {
                continue;
            }

            if ($onlyFiles === null) {
                return true;
            }

            return ! $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $onlyFiles);
        }

        return null;
    }
}
