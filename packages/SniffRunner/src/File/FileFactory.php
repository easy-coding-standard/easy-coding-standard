<?php declare(strict_types=1);

namespace Symplify\SniffRunner\File;

use Nette\FileNotFoundException;
use Symplify\SniffRunner\Application\Fixer;
use Symplify\SniffRunner\Parser\FileToTokensParser;
use Symplify\SniffRunner\Report\ErrorDataCollector;

final class FileFactory
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var ErrorDataCollector
     */
    private $reportCollector;

    /**
     * @var FileToTokensParser
     */
    private $fileToTokenParser;

    public function __construct(
        Fixer $fixer,
        ErrorDataCollector $reportCollector,
        FileToTokensParser $fileToTokenParser
    ) {
        $this->fixer = $fixer;
        $this->reportCollector = $reportCollector;
        $this->fileToTokenParser = $fileToTokenParser;
    }

    public function create(string $filePath, bool $isFixer) : File
    {
        $this->ensureFileExists($filePath);

        $tokens = $this->fileToTokenParser->parseFromFilePath($filePath);

        return new File($filePath, $tokens, $this->fixer, $this->reportCollector, $isFixer, "\n");
    }

    private function ensureFileExists(string $filePath)
    {
        if (!is_file($filePath) || !file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File "%s" was not found.',
                $filePath
            ));
        }
    }
}
