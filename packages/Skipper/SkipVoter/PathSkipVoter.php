<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipVoter;

use Symplify\EasyCodingStandard\Skipper\Contract\SkipVoterInterface;
use Symplify\EasyCodingStandard\Skipper\Matcher\FileInfoMatcher;
use Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver\SkippedPathsResolver;

final class PathSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private readonly FileInfoMatcher $fileInfoMatcher,
        private readonly SkippedPathsResolver $skippedPathsResolver
    ) {
    }

    public function match(string | object $element): bool
    {
        return true;
    }

    public function shouldSkip(string | object $element, \SplFileInfo | string $file): bool
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($file, $skippedPaths);
    }
}
