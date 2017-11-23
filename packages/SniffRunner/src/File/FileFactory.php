<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use SplFileInfo;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\CurrentSniffProvider;
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

    /**
     * @var CurrentSniffProvider
     */
    private $currentSniffProvider;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var File[]
     */
    private $filesByHash = [];

    /**
     * @var CachedFileLoader
     */
    private $cachedFileLoader;

    public function __construct(
        Fixer $fixer,
        ErrorCollector $errorCollector,
        FileToTokensParser $fileToTokensParser,
        CurrentSniffProvider $currentSniffProvider,
        Skipper $skipper,
        CachedFileLoader $cachedFileLoader
    ) {
        $this->fixer = $fixer;
        $this->errorCollector = $errorCollector;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->currentSniffProvider = $currentSniffProvider;
        $this->skipper = $skipper;
        $this->cachedFileLoader = $cachedFileLoader;
    }

    public function createFromFileInfo(SplFileInfo $fileInfo, bool $isFixer): File
    {
        $fileHash = md5_file($fileInfo->getPathname());

        if (isset($this->filesByHash[$fileHash])) {
            return $this->filesByHash[$fileHash];
        }

        $filePathName = $fileInfo->getPathname();

        $file = new File(
            $filePathName,
            $this->cachedFileLoader->getFileContent($fileInfo),
            $this->fileToTokensParser->parseFromFilePath($filePathName),
            $this->fixer,
            $this->errorCollector,
            $isFixer,
            $this->currentSniffProvider,
            $this->skipper
        );

        return $this->filesByHash[$fileHash] = $file;
    }
}
