<?php

declare (strict_types=1);
namespace ECSPrefix202609\PhpParser\Lexer\TokenEmulator;

use ECSPrefix202609\PhpParser\PhpVersion;
// Retained for reverse emulation support only.
final class FnTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion(): PhpVersion
    {
        return PhpVersion::fromString('7.4');
    }
    public function getKeywordString(): string
    {
        return 'fn';
    }
    public function getKeywordToken(): int
    {
        return \T_FN;
    }
}
