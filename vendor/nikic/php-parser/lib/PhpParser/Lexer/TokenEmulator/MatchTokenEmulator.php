<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Lexer\TokenEmulator;

use ConfigTransformer20210601\PhpParser\Lexer\Emulative;
final class MatchTokenEmulator extends \ConfigTransformer20210601\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \ConfigTransformer20210601\PhpParser\Lexer\Emulative::PHP_8_0;
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
