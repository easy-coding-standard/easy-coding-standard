<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Webmozart\Assert\Assert;

/**
 * @phpstan-import-type SkipRules from ECSConfig
 */
final class SimpleParameterProvider
{
    /**
     * @var array<string, mixed>
     */
    private static array $parameters = [];

    public static function addParameter(string $key, mixed $value): void
    {
        if (is_array($value)) {
            $mergedParameters = $key === Option::SKIP
                ? self::mergeSkipRules($value)
                : array_merge(self::$parameters[$key] ?? [], $value);
            self::$parameters[$key] = $mergedParameters;
        } else {
            self::$parameters[$key][] = $value;
        }
    }

    public static function setParameter(string $key, mixed $value): void
    {
        self::$parameters[$key] = $value;
    }

    /**
     * @return mixed[]
     */
    public static function getArrayParameter(string $key): array
    {
        $parameter = self::$parameters[$key] ?? [];
        Assert::isArray($parameter);

        if (array_is_list($parameter)) {
            // remove duplicates
            return array_values(array_unique($parameter));
        }

        return $parameter;
    }

    public static function getStringParameter(string $key): string
    {
        return self::$parameters[$key];
    }

    public static function getIntParameter(string $key): int
    {
        return self::$parameters[$key];
    }

    public static function getBoolParameter(string $key): bool
    {
        return self::$parameters[$key];
    }

    /**
     * For cache invalidation
     */
    public static function hash(): string
    {
        return sha1(serialize(self::$parameters));
    }

    /**
     * @param SkipRules $skipRules
     * @return SkipRules
     */
    private static function mergeSkipRules(array $skipRules): array
    {
        $rules = self::getArrayParameter(Option::SKIP);

        foreach ($skipRules as $key => $value) {
            if (is_int($key)) {
                $rules[] = $value;
            }

            if (is_string($key)) {
                $existingRule = $rules[$key] ?? [];
                $additionalRule = $value ?? [];
                $rules[$key] = array_unique(array_filter(array_merge_recursive((array)$existingRule, (array)$additionalRule)));

                if ([] === $rules[$key]) {
                    $rules[$key] = null;
                }
            }
        }

        return $rules;
    }
}
