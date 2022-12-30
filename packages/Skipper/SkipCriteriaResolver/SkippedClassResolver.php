<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver;

use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;

final class SkippedClassResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private array $skippedClasses = [];

    public function __construct(
        private ParameterProvider $parameterProvider,
        private ClassLikeExistenceChecker $classLikeExistenceChecker
    ) {
    }

    /**
     * @return array<string, string[]|null>
     */
    public function resolve(): array
    {
        if ($this->skippedClasses !== []) {
            return $this->skippedClasses;
        }

        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);

        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (is_int($key)) {
                $key = $value;
                $value = null;
            }

            if (! is_string($key)) {
                continue;
            }

            if (! $this->classLikeExistenceChecker->doesClassLikeExist($key)) {
                continue;
            }

            $this->skippedClasses[$key] = $value;
        }

        return $this->skippedClasses;
    }
}
