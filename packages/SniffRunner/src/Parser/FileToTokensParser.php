<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Parser;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use PHP_CodeSniffer\Util\Common;
use stdClass;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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
    public function parseFromFileInfo(SmartFileInfo $smartFileInfo): array
    {
        $phpTokenizer = $this->createTokenizerFromFileInfo($smartFileInfo);

        return $phpTokenizer->getTokens();
    }

    public function createTokenizerFromFileInfo(SmartFileInfo $smartFileInfo): PHP
    {
        $fileContent = $this->cachedFileLoader->getFileContent($smartFileInfo);

        return new PHP($fileContent, $this->getLegacyConfig(), Common::detectLineEndings($fileContent));
    }

    public function detectLineEndingsFromFileInfo(SmartFileInfo $smartFileInfo): string
    {
        $fileContent = $this->cachedFileLoader->getFileContent($smartFileInfo);

        return Common::detectLineEndings($fileContent);
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
