<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver;

use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class SkippedMessagesResolver
{
    /**
     * @var array<string, string[]|null>
     */
    private array $skippedMessages = [];

    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @return array<string, string[]|null>
     */
    public function resolve(): array
    {
        if ($this->skippedMessages !== []) {
            return $this->skippedMessages;
        }

        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);

        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (is_int($key)) {
                $key = $value;
                $value = null;
            }

            if (! is_string($key)) {
                continue;
            }

            if (substr_count($key, ' ') === 0) {
                continue;
            }

            $this->skippedMessages[$key] = $value;
        }

        return $this->skippedMessages;
    }
}
