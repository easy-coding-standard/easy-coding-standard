<?php

namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\SmartFileSystem\SmartFileSystem;
final class FileToTokensParser
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @param string $filePath
     * @return \PhpCsFixer\Tokenizer\Tokens
     */
    public function parseFromFilePath($filePath)
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \PhpCsFixer\Tokenizer\Tokens::fromCode($fileContent);
    }
}
