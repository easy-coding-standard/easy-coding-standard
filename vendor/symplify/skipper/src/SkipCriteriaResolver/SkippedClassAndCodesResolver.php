<?php

namespace ECSPrefix20210516\Symplify\Skipper\SkipCriteriaResolver;

use ECSPrefix20210516\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210516\Symplify\Skipper\ValueObject\Option;
final class SkippedClassAndCodesResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private $skippedClassAndCodes = [];
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ECSPrefix20210516\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @return mixed[]
     */
    public function resolve()
    {
        if ($this->skippedClassAndCodes !== []) {
            return $this->skippedClassAndCodes;
        }
        $skip = $this->parameterProvider->provideArrayParameter(\ECSPrefix20210516\Symplify\Skipper\ValueObject\Option::SKIP);
        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (\is_int($key)) {
                $key = $value;
                $value = null;
            }
            if (\substr_count($key, '.') !== 1) {
                continue;
            }
            $this->skippedClassAndCodes[$key] = $value;
        }
        return $this->skippedClassAndCodes;
    }
}
