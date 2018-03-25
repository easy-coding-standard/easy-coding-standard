<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

final class FileDiffFactory
{
    /**
     * @param string[] $appliedCheckers
     */
    public static function createFromDiffAndAppliedCheckers(string $diff, array $appliedCheckers): FileDiff
    {
        return new FileDiff($diff, $appliedCheckers);
    }
}
