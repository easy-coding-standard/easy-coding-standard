<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Parser;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use SplFileInfo;
use stdClass;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;

final class FileToTokensParser
{
    /**
     * @var stdClass
     */
    private $legacyConfig;

    /**
     * @var CachedFileLoader
     */
    private $cachedFileLoader;

    public function __construct(CachedFileLoader $cachedFileLoader)
    {
        $this->cachedFileLoader = $cachedFileLoader;
    }

    /**
     * @return mixed[]
     */
    public function parseFromFilePath(string $filePath): array
    {
        $phpTokenizer = $this->createTokenizerFromFilePath($filePath);

        return $phpTokenizer->getTokens();
    }

    public function createTokenizerFromFilePath(string $filePath): PHP
    {
        $fileContent = $this->cachedFileLoader->getFileContent(new SplFileInfo($filePath));

        return new PHP($fileContent, $this->getLegacyConfig(), PHP_EOL);
    }

    /**
     * @return Config|stdClass
     */
    private function getLegacyConfig()
    {
        if ($this->legacyConfig) {
            return $this->legacyConfig;
        }

        $config = new stdClass();
        $config->tabWidth = 4;
        $config->annotations = false;

        return $this->legacyConfig = $config;
    }
}
