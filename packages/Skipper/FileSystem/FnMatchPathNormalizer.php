<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\FileSystem;

use Nette\Utils\Strings;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Skipper\FileSystem\FnMatchPathNormalizerTest
 */
final class FnMatchPathNormalizer
{
    /**
     * @var string
     * @see https://regex101.com/r/ZB2dFV/2
     */
    private const ONLY_ENDS_WITH_ASTERISK_REGEX = '#^[^*](.*?)\*$#';

    /**
     * @var string
     * @see https://regex101.com/r/aVUDjM/2
     */
    private const ONLY_STARTS_WITH_ASTERISK_REGEX = '#^\*(.*?)[^*]$#';

    public function normalizeForFnmatch(string $path): string
    {
        // ends with *
        if (Strings::match($path, self::ONLY_ENDS_WITH_ASTERISK_REGEX)) {
            return '*' . $path;
        }

        // starts with *
        if (Strings::match($path, self::ONLY_STARTS_WITH_ASTERISK_REGEX)) {
            return $path . '*';
        }

        if (\str_contains($path, '..')) {
            /** @var string|false $path */
            $path = realpath($path);
            if ($path === false) {
                return '';
            }
        }

        return $path;
    }
}
