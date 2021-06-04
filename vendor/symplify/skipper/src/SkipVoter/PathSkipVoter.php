<?php

declare (strict_types=1);
namespace ECSPrefix20210604\Symplify\Skipper\SkipVoter;

use ECSPrefix20210604\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210604\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20210604\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileInfo;
final class PathSkipVoter implements \ECSPrefix20210604\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;
    /**
     * @var SkippedPathsResolver
     */
    private $skippedPathsResolver;
    public function __construct(\ECSPrefix20210604\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher, \ECSPrefix20210604\Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver $skippedPathsResolver)
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
     */
    public function shouldSkip($element, \ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
