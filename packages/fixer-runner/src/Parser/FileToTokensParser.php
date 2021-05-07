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
    /**
     * @param \Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem
     */
    public function __construct($smartFileSystem)
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
        return Tokens::fromCode($fileContent);
    }
}
