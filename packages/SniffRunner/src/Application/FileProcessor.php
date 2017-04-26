<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\SniffRunner\Contract\SniffCollectorInterface;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\Legacy\LegacyCompatibilityLayer;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\Event\FileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\TokenDispatcher;

final class FileProcessor implements FileProcessorInterface, SniffCollectorInterface
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
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var bool
     */
    private $isFixer = false;

    /**
     * @var Sniff[]
     */
    private $sniffs = [];

    public function __construct(
        TokenDispatcher $tokenDispatcher,
        Fixer $fixer,
        FileFactory $fileFactory
    ) {
        $this->tokenDispatcher = $tokenDispatcher;
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;

        LegacyCompatibilityLayer::add();
    }

    public function addSniff(Sniff $sniff): void
    {
        $this->sniffs[] = $sniff;
    }

    public function setupWithCommand(RunCommand $runCommand): void
    {
//        $this->tokenDispatcher->addSniffListeners($sniffs);

        $this->isFixer = $runCommand->isFixer();
    }

    public function processFile(SplFileInfo $fileInfo): void
    {
        $file = $this->fileFactory->createFromFileInfo($fileInfo, $this->isFixer);

        if ($this->isFixer === false) {
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
