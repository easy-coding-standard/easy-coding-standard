<?php

namespace Symplify\Skipper\SkipVoter;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use Symplify\Skipper\Skipper\OnlySkipper;
use Symplify\Skipper\Skipper\SkipSkipper;
use Symplify\Skipper\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassSkipVoter implements SkipVoterInterface
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

    public function __construct(
        ClassLikeExistenceChecker $classLikeExistenceChecker,
        ParameterProvider $parameterProvider,
        SkipSkipper $skipSkipper,
        OnlySkipper $onlySkipper,
        SkippedClassResolver $skippedClassResolver
    ) {
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
        if (is_object($element)) {
            return true;
        }

        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }

    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo)
    {
        $only = $this->parameterProvider->provideArrayParameter(Option::ONLY);

        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($element, $smartFileInfo, $only);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $smartFileInfo, $skippedClasses);
    }
}
