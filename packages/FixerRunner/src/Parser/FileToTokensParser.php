<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Tokens;

final class FileToTokensParser
{
    /**
     * @var Tokens[]
     */
    private $tokensByFileHash = [];

    public function parseFromFilePath(string $filePath): Tokens
    {
        $fileHash = md5_file($filePath);

        if (isset($this->tokensByFileHash[$fileHash])) {
            return $this->tokensByFileHash[$fileHash];
        }

        $content = file_get_contents($filePath);

        // from array? - investigave transability from PHP_CodeSniffer

        return $this->tokensByFileHash[$fileHash] = Tokens::fromCode($content);
    }

    public function clearCache(): void
    {
        Tokens::clearCache();
    }
}
