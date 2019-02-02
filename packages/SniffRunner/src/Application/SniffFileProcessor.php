<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Application\CurrentCheckerProvider;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\DualRunAwareFileProcessorInterface;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SniffFileProcessor implements FileProcessorInterface, DualRunAwareFileProcessorInterface
{
    /**
     * @var bool
     */
    private $isSecondRunPrepared = false;

    /**
     * @var Sniff[]|DualRunInterface[]
     */
    private $sniffs = [];

    /**
     * @var Sniff[][]
     */
    private $tokenListeners = [];

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var DifferInterface
     */
    private $differ;

    /**
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    /**
     * @var CurrentCheckerProvider
     */
    private $currentCheckerProvider;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    /**
     * @param Sniff[] $sniffs
     */
    public function __construct(
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration,
        Skipper $skipper,
        ErrorAndDiffCollector $errorAndDiffCollector,
        DifferInterface $differ,
        AppliedCheckersCollector $appliedCheckersCollector,
        CurrentCheckerProvider $currentCheckerProvider,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        CurrentFileProvider $currentFileProvider,
        array $sniffs
    ) {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->skipper = $skipper;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->differ = $differ;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->currentCheckerProvider = $currentCheckerProvider;

        $this->addCompatibilityLayer();

        foreach ($sniffs as $sniff) {
            $this->addSniff($sniff);
        }
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->currentFileProvider = $currentFileProvider;
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
        return array_filter($this->sniffs, function (Sniff $sniff): bool {
            return $sniff instanceof DualRunInterface;
        });
    }

    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        // required for dual run
        $this->currentFileProvider->setFileInfo($smartFileInfo);

        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);

        // 1. puts tokens into fixer
        $this->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processTokens($file, $smartFileInfo);

        // 3. add diff
        if ($smartFileInfo->getContents() !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($smartFileInfo->getContents(), $this->fixer->getContents());

            $this->errorAndDiffCollector->addDiffForFileInfo(
                $smartFileInfo,
                $diff,
                $this->appliedCheckersCollector->getAppliedCheckersPerFileInfo($smartFileInfo)
            );
        }

        // 4. save file content (faster without changes check)
        if ($this->configuration->isFixer()) {
            FileSystem::write($file->getFilename(), $this->fixer->getContents());
        }

        return $this->fixer->getContents();
    }

    public function processFileSecondRun(SmartFileInfo $smartFileInfo): string
    {
        $this->prepareSecondRun();

        return $this->processFile($smartFileInfo);
    }

    private function addCompatibilityLayer(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
            new Tokens();
        }
    }

    private function processTokens(File $file, SmartFileInfo $smartFileInfo): void
    {
        foreach ($file->getTokens() as $position => $token) {
            if (! array_key_exists($token['code'], $this->tokenListeners)) {
                continue;
            }

            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipCheckerAndFile($sniff, $smartFileInfo)) {
                    continue;
                }

                $this->currentCheckerProvider->setChecker($sniff);
                if ($this->easyCodingStandardStyle->isDebug()) {
                    $this->easyCodingStandardStyle->writeln(get_class($sniff));
                }

                $sniff->process($file, $position);
            }
        }
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
