<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Application\CurrentCheckerProvider;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\Parser\FileToTokensParser;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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

    /**
     * @var CurrentCheckerProvider
     */
    private $currentCheckerProvider;

    public function __construct(
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        FileToTokensParser $fileToTokensParser,
        CurrentCheckerProvider $currentCheckerProvider,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        CurrentFileProvider $currentFileProvider
    ) {
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->currentFileProvider = $currentFileProvider;
        $this->currentCheckerProvider = $currentCheckerProvider;
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): File
    {
        $fileTokens = $this->fileToTokensParser->parseFromFileInfo($smartFileInfo);

        $file = new File(
            $smartFileInfo->getRelativeFilePath(),
            $fileTokens,
            $this->fixer,
            $this->errorAndDiffCollector,
            $this->currentCheckerProvider,
            $this->skipper,
            $this->appliedCheckersCollector,
            $this->currentFileProvider
        );

        $file->eolChar = $this->fileToTokensParser->detectLineEndingsFromFileInfo($smartFileInfo);

        // BC layer
        $file->tokenizer = $this->fileToTokensParser->createTokenizerFromFileInfo($smartFileInfo);

        return $file;
    }
}
