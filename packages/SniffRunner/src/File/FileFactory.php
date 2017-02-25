<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

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

    public function create(string $filePath, bool $isFixer): File
    {
        $this->ensureFileExists($filePath);

        $tokens = $this->fileToTokenParser->parseFromFilePath($filePath);

        return new File($filePath, $tokens, $this->fixer, $this->reportCollector, $isFixer);
    }

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
