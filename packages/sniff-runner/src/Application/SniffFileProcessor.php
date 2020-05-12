<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SniffFileProcessor implements FileProcessorInterface
{
    /**
     * @var Sniff[]
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
     * @param Sniff[] $sniffs
     */
    public function __construct(
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration,
        ErrorAndDiffCollector $errorAndDiffCollector,
        DifferInterface $differ,
        AppliedCheckersCollector $appliedCheckersCollector,
        array $sniffs = []
    ) {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->differ = $differ;
        $this->appliedCheckersCollector = $appliedCheckersCollector;

        $this->addCompatibilityLayer();

        foreach ($sniffs as $sniff) {
            $this->addSniff($sniff);
        }
    }

    public function addSniff(Sniff $sniff): void
    {
        $this->sniffs[] = $sniff;
        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }

    /**
     * @return Sniff[]
     */
    public function getCheckers(): array
    {
        return $this->sniffs;
    }

    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);

        // mimic original behavior
        /** mimics @see \PHP_CodeSniffer\Files\File::process() */
        /** mimics @see \PHP_CodeSniffer\Fixer::fixFile() */
        $this->fixFile($file, $this->fixer, $smartFileInfo, $this->tokenListeners);

        // add diff
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

    private function addCompatibilityLayer(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
            new Tokens();
        }
    }

    /**
     * @param Sniff[][] $tokenListeners
     */
    private function fixFile(File $file, Fixer $fixer, SmartFileInfo $smartFileInfo, array $tokenListeners): void
    {
        $previousContent = $smartFileInfo->getContents();
        $this->fixer->loops = 0;

        do {
            // Only needed once file content has changed.
            $content = $previousContent;

            $file->setContent($content);
            $file->processWithTokenListenersAndFileInfo($tokenListeners, $smartFileInfo);

            // fixed content
            $previousContent = $fixer->getContents();
            ++$this->fixer->loops;
        } while ($previousContent !== $content);
    }
}
