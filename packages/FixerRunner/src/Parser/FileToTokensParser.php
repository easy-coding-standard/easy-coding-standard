<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;

final class FileToTokensParser
{
    /**
     * @var CachedFileLoader
     */
    private $cachedFileLoader;

    public function __construct(CachedFileLoader $cachedFileLoader)
    {
        $this->cachedFileLoader = $cachedFileLoader;
    }

    public function parseFromFilePath(string $filePath): Tokens
    {
        return Tokens::fromCode($this->cachedFileLoader->getFileContent(new SplFileInfo($filePath)));
    }
}
