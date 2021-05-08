<?php

namespace Symplify\Skipper\SkipVoter;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use Symplify\SmartFileSystem\SmartFileInfo;
final class PathSkipVoter implements \Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;
    /**
     * @var SkippedPathsResolver
     */
    private $skippedPathsResolver;
    public function __construct(\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher, \Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver $skippedPathsResolver)
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
    public function shouldSkip($element, \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
