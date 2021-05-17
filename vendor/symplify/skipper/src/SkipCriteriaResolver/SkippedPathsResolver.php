<?php

namespace ECSPrefix20210517\Symplify\Skipper\SkipCriteriaResolver;

use ECSPrefix20210517\Nette\Utils\Strings;
use ECSPrefix20210517\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210517\Symplify\Skipper\ValueObject\Option;
use ECSPrefix20210517\Symplify\SmartFileSystem\Normalizer\PathNormalizer;
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
    public function __construct(\ECSPrefix20210517\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \ECSPrefix20210517\Symplify\SmartFileSystem\Normalizer\PathNormalizer $pathNormalizer)
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
        $skip = $this->parameterProvider->provideArrayParameter(\ECSPrefix20210517\Symplify\Skipper\ValueObject\Option::SKIP);
        foreach ($skip as $key => $value) {
            if (!\is_int($key)) {
                continue;
            }
            if (\file_exists($value)) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
                continue;
            }
            if (\ECSPrefix20210517\Nette\Utils\Strings::contains($value, '*')) {
                $this->skippedPaths[] = $this->pathNormalizer->normalizePath($value);
                continue;
            }
        }
        return $this->skippedPaths;
    }
}
