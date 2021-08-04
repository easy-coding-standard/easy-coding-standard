<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Lexer\TokenEmulator;

use ECSPrefix20210804\PhpParser\Lexer\Emulative;
final class FnTokenEmulator extends \ECSPrefix20210804\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \ECSPrefix20210804\PhpParser\Lexer\Emulative::PHP_7_4;
    }
    public function getKeywordString() : string
    {
        return 'fn';
    }
    public function getKeywordToken() : int
    {
        return \T_FN;
    }
}
