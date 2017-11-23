<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;

final class SniffFileProcessor implements FileProcessorInterface
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Sniff[]|DualRunInterface[]
     */
    private $sniffs = [];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var bool
     */
    private $isFixer = false;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var Sniff[][]
     */
    private $tokenListeners = [];

    /**
     * @var CheckerMetricRecorder
     */
    private $checkerMetricRecorder;

    /**
     * @var CurrentSniffProvider
     */
    private $currentSniffProvider;

    /**
     * @var bool
     */
    private $isSecondRunPrepared = false;

    public function __construct(
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration,
        Skipper $skipper,
        CheckerMetricRecorder $checkerMetricRecorder,
        CurrentSniffProvider $currentSniffProvider
    ) {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->skipper = $skipper;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
        $this->currentSniffProvider = $currentSniffProvider;

        $this->addCompatibilityLayer();
    }

    public function addSniff(Sniff $sniff): void
    {
        $this->sniffs[] = $sniff;
        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }

    public function setSingleSniff(Sniff $sniff): void
    {
        $this->tokenListeners = [];
        $this->addSniff($sniff);
    }

    /**
     * @return Sniff[]|DualRunInterface[]
     */
    public function getSniffs(): array
    {
        return $this->sniffs;
    }

    /**
     * @return DualRunInterface[]|Sniff[]
     */
    public function getDualRunSniffs(): array
    {
        return array_filter($this->sniffs, function (Sniff $sniff) {
            return $sniff instanceof DualRunInterface;
        });
    }

    public function processFile(SplFileInfo $fileInfo, bool $dryRun = false): void
    {
        $file = $this->fileFactory->createFromFileInfo($fileInfo, $this->isFixer());

        if ($this->isFixer() === false) {
            $this->processFileWithoutFixer($file, $fileInfo);
        } else {
            $this->processFileWithFixer($file, $dryRun, $fileInfo);
        }
    }

    public function processFileSecondRun(SplFileInfo $fileInfo, bool $dryRun = false): void
    {
        $this->prepareSecondRun();

        $file = $this->fileFactory->createFromFileInfo($fileInfo, $this->isFixer());

        if ($this->isFixer() === false) {
            $this->processFileWithoutFixer($file, $fileInfo);
        } else {
            $this->processFileWithFixer($file, $dryRun, $fileInfo);
        }
    }

    public function setIsFixer(bool $isFixer): void
    {
        $this->isFixer = $isFixer;
    }

    private function processFileWithoutFixer(File $file, SplFileInfo $fileInfo): void
    {
        foreach ($file->getTokens() as $position => $token) {
            if (! array_key_exists($token['code'], $this->tokenListeners)) {
                continue;
            }

            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipCheckerAndFile($sniff, $fileInfo->getRealPath())) {
                    continue;
                }

                $this->checkerMetricRecorder->startWithChecker($sniff);
                $this->currentSniffProvider->setSniff($sniff);
                $sniff->process($file, $position);
                $this->checkerMetricRecorder->endWithChecker($sniff);
            }
        }
    }

    private function processFileWithFixer(File $file, bool $dryRun, SplFileInfo $fileInfo): void
    {
        // 1. puts tokens into fixer
        $this->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFileWithoutFixer($file, $fileInfo);

        // 3. save file content (faster without changes check)
        if ($dryRun === false) {
            file_put_contents($file->getFilename(), $this->fixer->getContents());
        }
    }

    private function addCompatibilityLayer(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
            new Tokens();
        }
    }

    private function isFixer(): bool
    {
        return $this->isFixer || $this->configuration->isFixer();
    }

    private function prepareSecondRun(): void
    {
        if ($this->isSecondRunPrepared) {
            return;
        }

        $this->tokenListeners = [];
        $dualRunSniffs = $this->getDualRunSniffs();
        $this->sniffs = [];

        foreach ($this->getDualRunSniffs() as $sniff) {
            $sniff->increaseRun();
            $this->addSniff($sniff);
        }

        $this->isSecondRunPrepared = true;
    }
}
