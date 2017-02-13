<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Files\File;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\SniffDispatcher;
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

    public function __construct(SniffDispatcher $sniffDispatcher, Fixer $fixer)
    {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->fixer = $fixer;
    }

    public function processFiles(array $files, bool $isFixer)
    {
        foreach ($files as $file) {
            $this->processFile($file, $isFixer);
        }
    }

    private function processFile(File $file, bool $isFixer)
    {
        if ($isFixer === false) {
            $this->processFileWithFixer($file);
        } else {
            $this->processFileWithoutFixer($file);
        }
    }

    private function processFileWithoutFixer(File $file)
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
            );
        }
    }

    private function processFileWithFixer(File $file)
    {
        // 1. puts tokens into fixer
        $file->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFileWithoutFixer($file);

        // 3. content has changed, save it!
        $newContent = $file->fixer->getContents();

        file_put_contents($file->getFilename(), $newContent);
    }
}
