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
    private $reportCollector;

    /**
     * @var FileToTokensParser
     */
    private $fileToTokenParser;

    public function __construct(Fixer $fixer, ErrorCollector $reportCollector, FileToTokensParser $fileToTokenParser)
    {
        $this->fixer = $fixer;
        $this->reportCollector = $reportCollector;
        $this->fileToTokenParser = $fileToTokenParser;
    }

    public function createFromFileInfo(SplFileInfo $fileInfo, bool $isFixer): File
    {
        $file = $fileInfo->getPathname();

        $tokens = $this->fileToTokenParser->parseFromFilePath($file);

        return new File($file, $tokens, $this->fixer, $this->reportCollector, $isFixer);
    }
}
