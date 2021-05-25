<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileSystem;
final class FileToTokensParser
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }
    public function parseFromFilePath(string $filePath) : \PhpCsFixer\Tokenizer\Tokens
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \PhpCsFixer\Tokenizer\Tokens::fromCode($fileContent);
    }
}
