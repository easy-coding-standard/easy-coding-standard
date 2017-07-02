<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Event\FileTokenEvent;
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
     * @var Sniff[]
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

    public function __construct(
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration,
        Skipper $skipper
    ) {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->skipper = $skipper;
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
     * @return Sniff[]
     */
    public function getSniffs(): array
    {
        return $this->sniffs;
    }

    public function processFile(SplFileInfo $fileInfo, bool $dryRun = false): void
    {
        $file = $this->fileFactory->createFromFileInfo($fileInfo, $this->isFixer());

        if ($this->isFixer() === false) {
            $this->processFileWithoutFixer($file);
        } else {
            $this->processFileWithFixer($file, $dryRun);
        }
    }

    public function setIsFixer(bool $isFixer): void
    {
        $this->isFixer = $isFixer;
    }

    private function processFileWithoutFixer(File $file): void
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->dispatchToken($token['code'], new FileTokenEvent($file, $stackPointer));
        }
    }

    private function processFileWithFixer(File $file, bool $dryRun = false): void
    {
        // 1. puts tokens into fixer
        $this->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFileWithoutFixer($file);

        // 3. content has changed, save it!
        $newContent = $this->fixer->getContents();

        if ($dryRun === false) {
            file_put_contents($file->getFilename(), $newContent);
        }
    }

    private function addCompatibilityLayer(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
        new Tokens;
    }

    private function isFixer(): bool
    {
        return $this->isFixer || $this->configuration->isFixer();
    }

    /**
     * @param int|string $token
     */
    private function dispatchToken($token, FileTokenEvent $fileTokenEvent): void
    {
        $tokenListeners = $this->tokenListeners[$token] ?? [];
        if (! count($tokenListeners)) {
            return;
        }

        $file = $fileTokenEvent->getFile();

        foreach ($tokenListeners as $sniff) {
            if ($this->skipper->shouldSkipCheckerAndFile($sniff, $file->getFilename())) {
                return;
            }

            $sniff->process($file, $fileTokenEvent->getPosition());
        }
    }
}
