<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Parser;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use stdClass;

final class FileToTokensParser
{
    /**
     * @var stdClass
     */
    private $legacyConfig;

    /**
     * @var PHP[]
     */
    private $tokenizerPerFile = [];

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
        $md5File = md5_file($filePath);
        if (isset($this->tokenizerPerFile[$md5File])) {
            return $this->tokenizerPerFile[$md5File];
        }

        $fileContent = FileSystem::read($filePath);

        return $this->tokenizerPerFile[$md5File] = (new PHP($fileContent, $this->getLegacyConfig(), PHP_EOL));
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
