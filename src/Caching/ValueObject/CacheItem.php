<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching\ValueObject;

/**
 * Inspired by
 * https://github.com/phpstan/phpstan-src/commit/eeae2da7999b2e8b7b04542c6175d46f80c6d0b9#diff-6dc14f6222bf150e6840ca44a7126653052a1cedc6a149b4e5c1e1a2c80eacdc
 */
final class CacheItem
{
    public function __construct(
        private readonly string $variableKey,
        private readonly mixed $data
    ) {
    }

    /**
     * @param mixed[] $properties
     */
    public static function __set_state(array $properties): self
    {
        return new self($properties['variableKey'], $properties['data']);
    }

    public function isVariableKeyValid(string $variableKey): bool
    {
        return $this->variableKey === $variableKey;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
