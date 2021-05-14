<?php

namespace ECSPrefix20210514\Symplify\Skipper\SkipVoter;

use ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210514\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix20210514\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210514\Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use ECSPrefix20210514\Symplify\Skipper\Skipper\OnlySkipper;
use ECSPrefix20210514\Symplify\Skipper\Skipper\SkipSkipper;
use ECSPrefix20210514\Symplify\Skipper\ValueObject\Option;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
final class ClassSkipVoter implements \ECSPrefix20210514\Symplify\Skipper\Contract\SkipVoterInterface
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
    public function __construct(\ECSPrefix20210514\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker $classLikeExistenceChecker, \ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \ECSPrefix20210514\Symplify\Skipper\Skipper\SkipSkipper $skipSkipper, \ECSPrefix20210514\Symplify\Skipper\Skipper\OnlySkipper $onlySkipper, \ECSPrefix20210514\Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver $skippedClassResolver)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->parameterProvider = $parameterProvider;
        $this->skipSkipper = $skipSkipper;
        $this->onlySkipper = $onlySkipper;
        $this->skippedClassResolver = $skippedClassResolver;
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function match($element)
    {
        if (\is_object($element)) {
            return \true;
        }
        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkip($element, \ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $only = $this->parameterProvider->provideArrayParameter(\ECSPrefix20210514\Symplify\Skipper\ValueObject\Option::ONLY);
        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($element, $smartFileInfo, $only);
        if (\is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }
        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $smartFileInfo, $skippedClasses);
    }
}
