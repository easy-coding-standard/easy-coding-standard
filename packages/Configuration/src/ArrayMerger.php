<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class ArrayMerger
{
    /**
     * @param mixed[] $arrays
     * @return mixed[]
     */
    public static function mergeRecursively(array $arrays): array
    {
        return array_merge_recursive(...$arrays);
    }
}
