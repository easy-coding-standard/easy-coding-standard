<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

final class StaticFixtureFileFinder
{
    /**
     * @return string[]
     */
    public static function yieldFiles(string $directory, string $suffix = '*.php.inc'): array
    {
        $finder = Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = iterator_to_array($finder);

        $filePaths = array_keys($fileInfos);
        Assert::allString($filePaths);

        return $filePaths;
    }
}
