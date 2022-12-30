<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver;

use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class SkippedClassAndCodesResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private array $skippedClassAndCodes = [];

    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @return array<string, string[]|null>
     */
    public function resolve(): array
    {
        if ($this->skippedClassAndCodes !== []) {
            return $this->skippedClassAndCodes;
        }

        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);

        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (is_int($key)) {
                $key = $value;
                $value = null;
            }

            if (substr_count($key, '.') !== 1) {
                continue;
            }

            $this->skippedClassAndCodes[$key] = $value;
        }

        return $this->skippedClassAndCodes;
    }
}
