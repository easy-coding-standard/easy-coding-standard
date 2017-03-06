<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\Event\FileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\TokenDispatcher;

final class FileProcessor
{
    /**
     * @var TokenDispatcher
     */
    private $tokenDispatcher;

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    /**
     * @var ChangedFilesDetectorInterface
     */
    private $changedFilesDetector;

    public function __construct(
        TokenDispatcher $tokenDispatcher,
        Fixer $fixer,
        EasyCodingStandardStyle $style,
        ChangedFilesDetectorInterface $changedFilesDetector
    ) {
        $this->tokenDispatcher = $tokenDispatcher;
        $this->fixer = $fixer;
        $this->style = $style;
        $this->changedFilesDetector = $changedFilesDetector;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File[] $files
     * @param bool $isFixer
     */
    public function processFiles(array $files, bool $isFixer): void
    {
        foreach ($files as $file) {
            if ($this->changedFilesDetector->hasFileChanged($file->getFilename()) === false) {
                $this->style->advanceProgressBar();
                continue;
            }

            $this->processFile($file, $isFixer);
            $this->style->advanceProgressBar();
        }
    }

    private function processFile(File $file, bool $isFixer): void
    {
        if ($isFixer === false) {
            $this->processFileWithoutFixer($file);
        } else {
            $this->processFileWithFixer($file);
        }
    }

    private function processFileWithoutFixer(File $file): void
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->tokenDispatcher->dispatchToken(
                $token['code'],
                new FileTokenEvent($file, $stackPointer)
            );
        }
    }

    private function processFileWithFixer(File $file): void
    {
        // 1. puts tokens into fixer
        $this->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFileWithoutFixer($file);

        // 3. content has changed, save it!
        $newContent = $this->fixer->getContents();

        file_put_contents($file->getFilename(), $newContent);
    }
}
