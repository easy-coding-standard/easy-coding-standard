<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileSystem;
final class FileToTokensParser
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return Tokens<Token>
     */
    public function parseFromFilePath(string $filePath) : Tokens
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Tokens::fromCode($fileContent);
    }
}
