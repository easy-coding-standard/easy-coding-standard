<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\Skipper\SkipVoter;

use ECSPrefix20220220\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20220220\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20220220\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
final class PathSkipVoter implements \ECSPrefix20220220\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var \Symplify\Skipper\Matcher\FileInfoMatcher
     */
    private $fileInfoMatcher;
    /**
     * @var \Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver
     */
    private $skippedPathsResolver;
    public function __construct(\ECSPrefix20220220\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher, \ECSPrefix20220220\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver $skippedPathsResolver)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
        $this->skippedPathsResolver = $skippedPathsResolver;
    }
    /**
     * @param object|string $element
     */
    public function match($element) : bool
    {
        return \true;
    }
    /**
     * @param object|string $element
     */
    public function shouldSkip($element, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
