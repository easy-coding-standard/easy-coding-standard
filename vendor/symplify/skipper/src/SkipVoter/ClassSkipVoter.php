<?php

declare (strict_types=1);
namespace ECSPrefix20210605\Symplify\Skipper\SkipVoter;

use ECSPrefix20210605\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210605\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix20210605\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210605\Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use ECSPrefix20210605\Symplify\Skipper\Skipper\OnlySkipper;
use ECSPrefix20210605\Symplify\Skipper\Skipper\SkipSkipper;
use ECSPrefix20210605\Symplify\Skipper\ValueObject\Option;
use ECSPrefix20210605\Symplify\SmartFileSystem\SmartFileInfo;
final class ClassSkipVoter implements \ECSPrefix20210605\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var SkipSkipper
     */
    private $skipSkipper;
    /**
     * @var OnlySkipper
     */
    private $onlySkipper;
    /**
     * @var SkippedClassResolver
     */
    private $skippedClassResolver;
    public function __construct(\ECSPrefix20210605\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker $classLikeExistenceChecker, \ECSPrefix20210605\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \ECSPrefix20210605\Symplify\Skipper\Skipper\SkipSkipper $skipSkipper, \ECSPrefix20210605\Symplify\Skipper\Skipper\OnlySkipper $onlySkipper, \ECSPrefix20210605\Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver $skippedClassResolver)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->parameterProvider = $parameterProvider;
        $this->skipSkipper = $skipSkipper;
        $this->onlySkipper = $onlySkipper;
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
     */
    public function shouldSkip($element, \ECSPrefix20210605\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool
    {
        $only = $this->parameterProvider->provideArrayParameter(\ECSPrefix20210605\Symplify\Skipper\ValueObject\Option::ONLY);
        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($element, $smartFileInfo, $only);
        if (\is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }
        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $smartFileInfo, $skippedClasses);
    }
}
