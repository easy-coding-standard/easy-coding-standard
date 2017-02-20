<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;

final class FileProcessor
{
    /**
     * @var FixerInterface[]
     */
    private $fixers;

    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    public function __construct(ErrorDataCollector $errorDataCollector, EasyCodingStandardStyle $style)
    {
        $this->errorDataCollector = $errorDataCollector;
        $this->style = $style;
    }

    public function registerFixers(array $fixers)
    {
        $this->fixers = $fixers;
    }

    public function processFiles(array $files, bool $isFixer)
    {
        foreach ($files as $file) {
            $this->fixFile($file, $isFixer);
            $this->style->progressAdvance();

            // we do not need Tokens to still caching just fixed file - so clear the cache
            Tokens::clearCache();
        }
    }

    private function fixFile(SplFileInfo $file, bool $isFixer)
    {
        $old = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($old);
        $oldHash = $tokens->getCodeHash();

        $newHash = $oldHash;
        $new = $old;

        $appliedFixers = [];

        foreach ($this->fixers as $fixer) {
            if (! $fixer->supports($file) || ! $fixer->isCandidate($tokens)) {
                continue;
            }

            $fixer->fix($file, $tokens);

            if ($tokens->isChanged()) {
                $this->addErrorToErrorMessageCollector($file, $fixer, $tokens);

                $tokens->clearEmptyTokens();
                $tokens->clearChanged();
                $appliedFixers[] = $fixer->getName();

            }
        }

        if (! empty($appliedFixers)) {
            $new = $tokens->generateCode();
            $newHash = $tokens->getCodeHash();
        }

        // We need to check if content was changed and then applied changes.
        // But we can't simple check $appliedFixers, because one fixer may revert
        // work of other and both of them will mark collection as changed.
        // Therefore we need to check if code hashes changed.
        if ($oldHash !== $newHash) {
            if ($isFixer) {
                if (@file_put_contents($file->getRealPath(), $new) === false) {
                    $error = error_get_last();

                    throw new IOException(
                        sprintf(
                            'Failed to write file "%s", "%s".',
                            $file->getPathname(),
                            $error ? $error['message'] : 'no reason available'
                        ),
                        0,
                        null,
                        $file->getRealPath()
                    );
                }
            }
        }
    }

    private function detectChangedLineFromTokens(Tokens $tokens): int
    {
        $line = 0;
        foreach ($tokens as $token) {
            if ($token->getContent() === "\n") {
                $line++;
            }
            if ($token->isChanged()) {
                return $line;
            }
        }

        return 0;
    }

    private function addErrorToErrorMessageCollector(SplFileInfo $file, AbstractFixer $fixer, Tokens $tokens): void
    {
        $filePath = str_replace('//', '/', $file->getPathname());

        $this->errorDataCollector->addErrorMessage(
            $filePath,
            $this->prepareErrorMessage($fixer),
            $this->detectChangedLineFromTokens($tokens),
            get_class($fixer),
            [],
            true
        );
    }

    private function prepareErrorMessage(AbstractFixer $fixer): string
    {
        if ($fixer instanceof DefinedFixerInterface) {
            $definition = $fixer->getDefinition();
            return $definition->getSummary();
        }

        return $fixer->getName();
    }
}
