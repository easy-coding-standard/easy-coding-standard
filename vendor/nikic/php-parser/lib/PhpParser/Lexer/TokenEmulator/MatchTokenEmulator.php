<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\Lexer\TokenEmulator;

use ECSPrefix202608\PhpParser\PhpVersion;
final class MatchTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion(): PhpVersion
    {
        return PhpVersion::fromComponents(8, 0);
    }
    public function getKeywordString(): string
    {
        return 'match';
    }
    public function getKeywordToken(): int
    {
        return \T_MATCH;
    }
}
