<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;

final class Runner
{
    /**
     * @var DirectoryInterface
     */
    private $directory;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var FixerInterface[]
     */
    private $fixers;

    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    public function __construct(
        Finder $finder,
        bool $isDryRun,
        array $fixers,
        ErrorDataCollector $errorDataCollector
    ) {
        $this->finder = $finder;
        $this->fixers = $fixers;
        $this->isDryRun = $isDryRun;
        $this->errorDataCollector = $errorDataCollector;
        $this->directory = new Directory('');
    }

    public function fix() : array
    {
        $changed = [];

        foreach ($this->finder->getIterator() as $file) {
            $fixInfo = $this->fixFile($file); //, $collection->currentLintingResult());
            if ($fixInfo) {
                $name = $this->directory->getRelativePathTo($file);
                $changed[$name] = $fixInfo;
            }

            // we do not need Tokens to still caching just fixed file - so clear the cache
            Tokens::clearCache();
        }

        return $changed;
    }

    private function fixFile(SplFileInfo $file): array
    {
        $old = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($old);
        $oldHash = $tokens->getCodeHash();

        $newHash = $oldHash;
        $new = $old;

        $appliedFixers = [];

        foreach ($this->fixers as $fixer) {
            if (!$fixer->supports($file) || !$fixer->isCandidate($tokens)) {
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

        $fixInfo = [];

        if (!empty($appliedFixers)) {
            $new = $tokens->generateCode();
            $newHash = $tokens->getCodeHash();
        }

        // We need to check if content was changed and then applied changes.
        // But we can't simple check $appliedFixers, because one fixer may revert
        // work of other and both of them will mark collection as changed.
        // Therefore we need to check if code hashes changed.
        if ($oldHash !== $newHash) {
            if (!$this->isDryRun) {
                if (false === @file_put_contents($file->getRealPath(), $new)) {
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

            $fixInfo = [
                'appliedFixers' => $appliedFixers,
            ];
        }

        return $fixInfo;
    }

    private function detectChangedLineFromTokens(Tokens $tokens) : int
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
