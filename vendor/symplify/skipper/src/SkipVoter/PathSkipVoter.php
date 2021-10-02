<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\Skipper\SkipVoter;

use ECSPrefix20211002\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20211002\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20211002\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
final class PathSkipVoter implements \ECSPrefix20211002\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var \Symplify\Skipper\Matcher\FileInfoMatcher
     */
    private $fileInfoMatcher;
    /**
     * @var \Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver
     */
    private $skippedPathsResolver;
    public function __construct(\ECSPrefix20211002\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher, \ECSPrefix20211002\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver $skippedPathsResolver)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
        $this->skippedPathsResolver = $skippedPathsResolver;
    }
    /**
     * @param string|object $element
     */
    public function match($element) : bool
    {
        return \true;
    }
    /**
     * @param string|object $element
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    public function shouldSkip($element, $smartFileInfo) : bool
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
