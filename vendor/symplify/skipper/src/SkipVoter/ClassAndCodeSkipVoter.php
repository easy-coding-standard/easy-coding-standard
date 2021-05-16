<?php

namespace ECSPrefix20210516\Symplify\Skipper\SkipVoter;

use ECSPrefix20210516\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210516\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20210516\Symplify\Skipper\SkipCriteriaResolver\SkippedClassAndCodesResolver;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * Matching class and code, e.g. App\Category\ArraySniff.SomeCode
 */
final class ClassAndCodeSkipVoter implements \ECSPrefix20210516\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var SkippedClassAndCodesResolver
     */
    private $skippedClassAndCodesResolver;
    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;
    public function __construct(\ECSPrefix20210516\Symplify\Skipper\SkipCriteriaResolver\SkippedClassAndCodesResolver $skippedClassAndCodesResolver, \ECSPrefix20210516\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher)
    {
        $this->skippedClassAndCodesResolver = $skippedClassAndCodesResolver;
        $this->fileInfoMatcher = $fileInfoMatcher;
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function match($element)
    {
        if (!\is_string($element)) {
            return \false;
        }
        return \substr_count($element, '.') === 1;
    }
    /**
     * @param string $element
     * @return bool
     */
    public function shouldSkip($element, \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $skippedClassAndCodes = $this->skippedClassAndCodesResolver->resolve();
        if (!\array_key_exists($element, $skippedClassAndCodes)) {
            return \false;
        }
        // skip regardless the path
        $skippedPaths = $skippedClassAndCodes[$element];
        if ($skippedPaths === null) {
            return \true;
        }
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
