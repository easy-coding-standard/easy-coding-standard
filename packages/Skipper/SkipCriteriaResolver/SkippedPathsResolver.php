<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver;

use Symplify\EasyCodingStandard\FileSystem\PathNormalizer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202302\Symplify\PackageBuilder\Parameter\ParameterProvider;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Skipper\SkipCriteriaResolver\SkippedPathsResolver\SkippedPathsResolverTest
 */
final class SkippedPathsResolver
{
    /**
     * @var string[]
     */
    private $skippedPaths = [];
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\FileSystem\PathNormalizer
     */
    private $pathNormalizer;
    public function __construct(ParameterProvider $parameterProvider, PathNormalizer $pathNormalizer)
    {
        $this->parameterProvider = $parameterProvider;
        $this->pathNormalizer = $pathNormalizer;
    }
    /**
     * @return string[]
     */
    public function resolve() : array
    {
        if ($this->skippedPaths !== []) {
            return $this->skippedPaths;
        }
        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);
        foreach ($skip as $key => $value) {
            if (!\is_int($key)) {
                continue;
            }
            if (\file_exists($value)) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
                continue;
            }
            if (\strpos((string) $value, '*') !== \false) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
            }
        }
        return $this->skippedPaths;
    }
}
