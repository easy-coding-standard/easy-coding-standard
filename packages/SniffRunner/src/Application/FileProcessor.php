<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\SniffDispatcher;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;

final class FileProcessor
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    public function __construct(SniffDispatcher $sniffDispatcher, Fixer $fixer, EasyCodingStandardStyle $style)
    {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->fixer = $fixer;
        $this->style = $style;
    }

    public function processFiles(array $files, bool $isFixer)
    {
        foreach ($files as $file) {
            $this->processFile($file, $isFixer);
            $this->style->progressAdvance();
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
            $this->sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
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
