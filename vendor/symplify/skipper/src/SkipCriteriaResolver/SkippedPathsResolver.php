<?php

namespace Symplify\Skipper\SkipCriteriaResolver;

use ECSPrefix20210507\Nette\Utils\Strings;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Skipper\ValueObject\Option;
use Symplify\SmartFileSystem\Normalizer\PathNormalizer;
/**
 * @see \Symplify\Skipper\Tests\SkipCriteriaResolver\SkippedPathsResolver\SkippedPathsResolverTest
 */
final class SkippedPathsResolver
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var string[]
     */
    private $skippedPaths = [];
    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;
    /**
     * @param \Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider
     * @param \Symplify\SmartFileSystem\Normalizer\PathNormalizer $pathNormalizer
     */
    public function __construct($parameterProvider, $pathNormalizer)
    {
        $this->parameterProvider = $parameterProvider;
        $this->pathNormalizer = $pathNormalizer;
    }
    /**
     * @return mixed[]
     */
    public function resolve()
    {
        if ($this->skippedPaths !== []) {
            return $this->skippedPaths;
        }
        $skip = $this->parameterProvider->provideArrayParameter(\Symplify\Skipper\ValueObject\Option::SKIP);
        foreach ($skip as $key => $value) {
            if (!\is_int($key)) {
                continue;
            }
            if (\file_exists($value)) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
                continue;
            }
            if (\ECSPrefix20210507\Nette\Utils\Strings::contains($value, '*')) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
                continue;
            }
        }
        return $this->skippedPaths;
    }
}
