<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use SplFileInfo;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\Parser\FileToTokensParser;

final class FileFactory
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FileToTokensParser
     */
    private $fileToTokensParser;

    public function __construct(Fixer $fixer, ErrorCollector $errorCollector, FileToTokensParser $fileToTokensParser)
    {
        $this->fixer = $fixer;
        $this->errorCollector = $errorCollector;
        $this->fileToTokensParser = $fileToTokensParser;
    }

    public function createFromFileInfo(SplFileInfo $fileInfo, bool $isFixer): File
    {
        $file = $fileInfo->getPathname();

        $tokens = $this->fileToTokensParser->parseFromFilePath($file);

        return new File($file, $tokens, $this->fixer, $this->errorCollector, $isFixer);
    }
}
