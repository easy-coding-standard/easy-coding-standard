<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use SplFileInfo;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\FileNotFoundException;
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

    public function createFromFileInfo(SplFileInfo $filePath, bool $isFixer): File
    {
        $file = $filePath->getPathname();
        $this->ensureFileExists($file);

        $tokens = $this->fileToTokenParser->parseFromFilePath($file);

        return new File($file, $tokens, $this->fixer, $this->reportCollector, $isFixer);
    }

    // @todo: already checked in layer above
    private function ensureFileExists(string $filePath): void
    {
        if (! is_file($filePath) || ! file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File "%s" was not found.',
                $filePath
            ));
        }
    }
}
