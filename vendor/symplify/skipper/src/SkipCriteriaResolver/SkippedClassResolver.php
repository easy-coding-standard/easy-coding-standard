<?php

namespace Symplify\Skipper\SkipCriteriaResolver;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\ValueObject\Option;
final class SkippedClassResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private $skippedClasses = [];
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    public function __construct(\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->parameterProvider = $parameterProvider;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }
    /**
     * @return mixed[]
     */
    public function resolve()
    {
        if ($this->skippedClasses !== []) {
            return $this->skippedClasses;
        }
        $skip = $this->parameterProvider->provideArrayParameter(\Symplify\Skipper\ValueObject\Option::SKIP);
        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (\is_int($key)) {
                $key = $value;
                $value = null;
            }
            if (!\is_string($key)) {
                continue;
            }
            if (!$this->classLikeExistenceChecker->doesClassLikeExist($key)) {
                continue;
            }
            $this->skippedClasses[$key] = $value;
        }
        return $this->skippedClasses;
    }
}
