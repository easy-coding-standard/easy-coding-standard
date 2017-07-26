<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff;

final class SniffNaming
{
    public function guessSniffClassByName(string $name): string
    {
        $parts = explode('.', $name);

        $sniffName = sprintf(
            'PHP_CodeSniffer\Standards\%s\Sniffs\%s\%sSniff',
            $parts[0],
            $parts[1],
            $parts[2]
        );

        return $sniffName;
    }

    public function isSniffName(string $possibleSniffCode): bool
    {
        return substr_count($possibleSniffCode, '.') === 3;
    }
}
