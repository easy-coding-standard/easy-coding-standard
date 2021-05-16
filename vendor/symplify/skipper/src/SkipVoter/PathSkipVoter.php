<?php

namespace ECSPrefix20210516\Symplify\Skipper\SkipVoter;

use ECSPrefix20210516\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210516\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20210516\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
final class PathSkipVoter implements \ECSPrefix20210516\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;
    /**
     * @var SkippedPathsResolver
     */
    private $skippedPathsResolver;
    public function __construct(\ECSPrefix20210516\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher, \ECSPrefix20210516\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver $skippedPathsResolver)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
        $this->skippedPathsResolver = $skippedPathsResolver;
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function match($element)
    {
        return \true;
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkip($element, \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
