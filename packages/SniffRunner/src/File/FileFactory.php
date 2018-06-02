<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
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
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

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
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    /**
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    public function __construct(
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        FileToTokensParser $fileToTokensParser,
        CurrentSniffProvider $currentSniffProvider,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        CurrentFileProvider $currentFileProvider
    ) {
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->currentSniffProvider = $currentSniffProvider;
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->currentFileProvider = $currentFileProvider;
    }

    public function createFromFileInfo(SplFileInfo $fileInfo): File
    {
        $fileTokens = $this->fileToTokensParser->parseFromFileInfo($fileInfo);

        $file = new File(
            $fileInfo->getPathname(),
            $fileTokens,
            $this->fixer,
            $this->errorAndDiffCollector,
            $this->currentSniffProvider,
            $this->skipper,
            $this->appliedCheckersCollector,
            $this->currentFileProvider
        );

        // BC layer
        $file->tokenizer = $this->fileToTokensParser->createTokenizerFromFileInfo($fileInfo);

        return $file;
    }
}
