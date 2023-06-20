<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Webmozart\Assert\Assert;

final class SimpleParameterProvider
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    public function addParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function getStringParameter(string $key): string
    {
        return $this->parameters[$key] ?? '';
    }

    /**
     * @return string[]
     */
    public function getArrayParameter(string $key): array
    {
        $parameter = $this->parameters[$key] ?? [];
        Assert::allString($parameter);

        return $parameter;
    }
}
