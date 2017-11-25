<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
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

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var DifferInterface
     */
    private $differ;

    public function __construct(
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration,
        Skipper $skipper,
        CheckerMetricRecorder $checkerMetricRecorder,
        CurrentSniffProvider $currentSniffProvider,
        ErrorCollector $errorCollector,
        DifferInterface $differ
    ) {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->skipper = $skipper;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
        $this->currentSniffProvider = $currentSniffProvider;

        $this->addCompatibilityLayer();
        $this->errorCollector = $errorCollector;
        $this->differ = $differ;
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
    public function getCheckers(): array
    {
        return $this->sniffs;
    }

    /**
     * @return DualRunInterface[]|Sniff[]
     */
    public function getDualRunCheckers(): array
    {
        return array_filter($this->sniffs, function (Sniff $sniff) {
            return $sniff instanceof DualRunInterface;
        });
    }

    public function processFile(SplFileInfo $fileInfo, bool $dryRun = false): void
    {
        $file = $this->fileFactory->createFromFileInfo($fileInfo, $this->isFixer());

        // 1. puts tokens into fixer
        $this->fixer->beginChangeSet();
        $this->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processTokens($file, $fileInfo);

        // 3. add diff
        if ($fileInfo->getContents() !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($fileInfo->getContents(), $this->fixer->getContents());
            $this->errorCollector->addDiffForFile($fileInfo->getRelativePath(), $diff, []);
        }

        // 4. save file content (faster without changes check)
        if ($dryRun === false) {
            file_put_contents($file->getFilename(), $this->fixer->getContents());
        }

        $file->cleanUp();
        $this->fixer->endChangeSet();
    }

    public function processFileSecondRun(SplFileInfo $fileInfo, bool $dryRun = false): void
    {
        $this->prepareSecondRun();
        $this->processFile($fileInfo, $dryRun);
    }

    public function setIsFixer(bool $isFixer): void
    {
        $this->isFixer = $isFixer;
    }

    private function processTokens(File $file, SplFileInfo $fileInfo): void
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
        $dualRunSniffs = $this->getDualRunCheckers();
        $this->sniffs = [];

        foreach ($dualRunSniffs as $sniff) {
            $sniff->increaseRun();
            $this->addSniff($sniff);
        }

        $this->isSecondRunPrepared = true;
    }
}
