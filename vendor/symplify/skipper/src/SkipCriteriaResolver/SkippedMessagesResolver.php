<?php

namespace ECSPrefix20210514\Symplify\Skipper\SkipCriteriaResolver;

use ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210514\Symplify\Skipper\ValueObject\Option;
final class SkippedMessagesResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private $skippedMessages = [];
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @return mixed[]
     */
    public function resolve()
    {
        if ($this->skippedMessages !== []) {
            return $this->skippedMessages;
        }
        $skip = $this->parameterProvider->provideArrayParameter(\ECSPrefix20210514\Symplify\Skipper\ValueObject\Option::SKIP);
        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (\is_int($key)) {
                $key = $value;
                $value = null;
            }
            if (!\is_string($key)) {
                continue;
            }
            if (\substr_count($key, ' ') === 0) {
                continue;
            }
            $this->skippedMessages[$key] = $value;
        }
        return $this->skippedMessages;
    }
}
