<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Parser;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use stdClass;
use Symfony\Component\Finder\SplFileInfo;
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
    public function parseFromFileInfo(SplFileInfo $filePath): array
    {
        $phpTokenizer = $this->createTokenizerFromFileInfo($filePath);

        return $phpTokenizer->getTokens();
    }

    public function createTokenizerFromFileInfo(SplFileInfo $filePath): PHP
    {
        $fileContent = $this->cachedFileLoader->getFileContent($filePath);

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
        $config->encoding = 'UTF-8';

        return $this->legacyConfig = $config;
    }
}
