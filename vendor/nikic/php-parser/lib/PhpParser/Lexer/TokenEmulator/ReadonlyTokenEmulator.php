<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Lexer\TokenEmulator;

use ECSPrefix20210804\PhpParser\Lexer\Emulative;
final class ReadonlyTokenEmulator extends \ECSPrefix20210804\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \ECSPrefix20210804\PhpParser\Lexer\Emulative::PHP_8_1;
    }
    public function getKeywordString() : string
    {
        return 'readonly';
    }
    public function getKeywordToken() : int
    {
        return \T_READONLY;
    }
}
