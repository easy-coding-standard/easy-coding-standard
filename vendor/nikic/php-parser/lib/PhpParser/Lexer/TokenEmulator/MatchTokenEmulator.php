<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Lexer\TokenEmulator;

use ECSPrefix20210804\PhpParser\Lexer\Emulative;
final class MatchTokenEmulator extends \ECSPrefix20210804\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \ECSPrefix20210804\PhpParser\Lexer\Emulative::PHP_8_0;
    }
    public function getKeywordString() : string
    {
        return 'match';
    }
    public function getKeywordToken() : int
    {
        return \T_MATCH;
    }
}
