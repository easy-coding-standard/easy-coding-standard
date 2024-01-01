<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver;

use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\FileSystem\PathNormalizer;
use Symplify\EasyCodingStandard\ValueObject\Option;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Skipper\SkipCriteriaResolver\SkippedPathsResolver\SkippedPathsResolverTest
 */
final class SkippedPathsResolver
{
    /**
     * @var string[]
     */
    private array $skippedPaths = [];

    public function __construct(
        private readonly PathNormalizer $pathNormalizer
    ) {
    }

    /**
     * @return string[]
     */
    public function resolve(): array
    {
        if ($this->skippedPaths !== []) {
            return $this->skippedPaths;
        }

        $skip = SimpleParameterProvider::getArrayParameter(Option::SKIP);
        foreach ($skip as $key => $value) {
            if (! is_int($key)) {
                continue;
            }

            if (\str_contains((string) $value, '*')) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
                continue;
            }

            if (file_exists($value)) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
            }
        }

        return $this->skippedPaths;
    }
}
