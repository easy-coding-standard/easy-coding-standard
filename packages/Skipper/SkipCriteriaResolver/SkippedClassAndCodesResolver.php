<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver;

use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202302\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202302\Webmozart\Assert\Assert;
final class SkippedClassAndCodesResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private $skippedClassAndCodes = [];
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @return array<string, string[]|null>
     */
    public function resolve() : array
    {
        if ($this->skippedClassAndCodes !== []) {
            return $this->skippedClassAndCodes;
        }
        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);
        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (\is_int($key)) {
                $key = $value;
                $value = null;
            }
            if (\substr_count((string) $key, '.') !== 1) {
                continue;
            }
            Assert::string($key);
            $this->skippedClassAndCodes[$key] = $value;
        }
        return $this->skippedClassAndCodes;
    }
}
