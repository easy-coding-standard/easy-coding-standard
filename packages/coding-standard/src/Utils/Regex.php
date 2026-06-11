<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Utils;

/**
 * Lightweight regex helper to avoid a runtime dependency on nette/utils.
 */
final class Regex
{
    /**
     * @return array<string, string>|null
     */
    public static function match(string $subject, string $pattern): ?array
    {
        $matches = [];
        if (preg_match($pattern, $subject, $matches) === 1) {
            return $matches;
        }
        return null;
    }
    /**
     * @return array<int, array<string, string>>
     */
    public static function matchAll(string $subject, string $pattern): array
    {
        $matches = [];
        preg_match_all($pattern, $subject, $matches, \PREG_SET_ORDER);
        return $matches;
    }
    /**
     * @param string|callable(array<int|string, mixed>): string $replacement
     */
    public static function replace(string $subject, string $pattern, $replacement = ''): string
    {
        if (is_callable($replacement)) {
            return (string) preg_replace_callback($pattern, $replacement, $subject);
        }
        return (string) preg_replace($pattern, $replacement, $subject);
    }
}
