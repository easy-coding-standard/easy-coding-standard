<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Tokens;

final class FileToTokensParser
{
    public function parseFromFilePath(string $filePath): Tokens
    {
        return Tokens::fromCode(file_get_contents($filePath));
    }
}
