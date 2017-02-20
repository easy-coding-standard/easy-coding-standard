<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Sorter;

use PHP_CodeSniffer\Sniffs\Sniff;

final class SniffSorter
{
    /**
     * @param Sniff[] $sniffs
     * @return Sniff[]
     */
    public static function sort(array $sniffs): array
    {
        usort($sniffs, function (Sniff $oneSniff, Sniff $otherSniff) {
            return strcmp(
                get_class($oneSniff),
                get_class($otherSniff)
            );
        });

        return $sniffs;
    }
}
