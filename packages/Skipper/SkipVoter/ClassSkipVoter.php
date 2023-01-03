<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipVoter;

use SplFileInfo;
use Symplify\EasyCodingStandard\Skipper\Contract\SkipVoterInterface;
use Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use Symplify\EasyCodingStandard\Skipper\Skipper\SkipSkipper;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;

final class ClassSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private readonly ClassLikeExistenceChecker $classLikeExistenceChecker,
        private readonly SkipSkipper $skipSkipper,
        private readonly SkippedClassResolver $skippedClassResolver
    ) {
    }

    public function match(string | object $element): bool
    {
        if (is_object($element)) {
            return true;
        }

        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }

    public function shouldSkip(string | object $element, SplFileInfo | string $file): bool
    {
        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $file, $skippedClasses);
    }
}
