<?php

declare (strict_types=1);
namespace ECSPrefix202208\Symplify\Skipper\SkipVoter;

use ECSPrefix202208\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix202208\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix202208\Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use ECSPrefix202208\Symplify\Skipper\Skipper\SkipSkipper;
use ECSPrefix202208\Symplify\SmartFileSystem\SmartFileInfo;
final class ClassSkipVoter implements SkipVoterInterface
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    /**
     * @var \Symplify\Skipper\Skipper\SkipSkipper
     */
    private $skipSkipper;
    /**
     * @var \Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver
     */
    private $skippedClassResolver;
    public function __construct(ClassLikeExistenceChecker $classLikeExistenceChecker, SkipSkipper $skipSkipper, SkippedClassResolver $skippedClassResolver)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->skipSkipper = $skipSkipper;
        $this->skippedClassResolver = $skippedClassResolver;
    }
    /**
     * @param string|object $element
     */
    public function match($element) : bool
    {
        if (\is_object($element)) {
            return \true;
        }
        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }
    /**
     * @param string|object $element
     * @param \Symplify\SmartFileSystem\SmartFileInfo|string $file
     */
    public function shouldSkip($element, $file) : bool
    {
        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $file, $skippedClasses);
    }
}
