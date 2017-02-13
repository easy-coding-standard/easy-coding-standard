<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

final class Runner
{
    /**
     * @var DifferInterface
     */
    private $differ;

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

    public function __construct(
        Finder $finder,
        array $fixers,
        DifferInterface $differ,
        bool $isDryRun
    ) {
        $this->finder = $finder;
        $this->fixers = $fixers;
        $this->differ = $differ;
        $this->isDryRun = $isDryRun;
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
                $tokens->clearEmptyTokens();
                $tokens->clearChanged();
                $appliedFixers[] = $fixer->getName();

                // todo: get changed content or line or sth to ErrorDataCollector
                // code: $fixer->getName()
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
                'diff' => $this->differ->diff($old, $new),
            ];
        }

        return $fixInfo;
    }
}
