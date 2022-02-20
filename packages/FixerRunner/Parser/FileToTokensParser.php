<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
final class FileToTokensParser
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return Tokens<Token>
     */
    public function parseFromFilePath(string $filePath) : \PhpCsFixer\Tokenizer\Tokens
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \PhpCsFixer\Tokenizer\Tokens::fromCode($fileContent);
    }
}
