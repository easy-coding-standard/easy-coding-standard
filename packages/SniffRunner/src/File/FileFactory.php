<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
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
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    public function __construct(
        Fixer $fixer,
        ErrorCollector $errorCollector,
        FileToTokensParser $fileToTokensParser,
        CurrentSniffProvider $currentSniffProvider,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector
    ) {
        $this->fixer = $fixer;
        $this->errorCollector = $errorCollector;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->currentSniffProvider = $currentSniffProvider;
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
    }

    public function createFromFileInfo(SplFileInfo $fileInfo): File
    {
        $fileHash = md5_file($fileInfo->getPathname());

        if (isset($this->filesByHash[$fileHash])) {
            return $this->filesByHash[$fileHash];
        }

        $filePathName = $fileInfo->getPathname();

        $file = new File(
            $filePathName,
            $this->fileToTokensParser->parseFromFilePath($filePathName),
            $this->fixer,
            $this->errorCollector,
            $this->currentSniffProvider,
            $this->skipper,
            $this->appliedCheckersCollector
        );

        return $this->filesByHash[$fileHash] = $file;
    }
}
