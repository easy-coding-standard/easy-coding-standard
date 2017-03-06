<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\Fixer\FixerFactory;
use Symplify\EasyCodingStandard\Skipper;

final class FileProcessor implements FileProcessorInterface
{
    /**
     * @var FixerInterface[]
     */
    private $fixers = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    public function __construct(ErrorCollector $errorCollector, Skipper $skipper, FixerFactory $fixerFactory)
    {
        $this->errorCollector = $errorCollector;
        $this->skipper = $skipper;
        $this->fixerFactory = $fixerFactory;
    }

//    /**

//     * @param SplFileInfo[] $files
//     * @param bool $isFixer
//     */
//    public function processFiles(array $files, bool $isFixer): void
//    {
//        foreach ($files as $file) {
//            $this->fixFile($file, $isFixer);
//
//            // we do not need Tokens to still caching just fixed file - so clear the cache
//            Tokens::clearCache();
//        }
//    }
    public function setupWithCommand(RunCommand $runCommand): void
    {
        $fixers = $this->fixerFactory->createFromClasses($runCommand->getFixers());
        $this->registerFixers($fixers);
    }

    /**
     * @todo this method is too long, decouple to smallerones
     */
    public function processFile(SplFileInfo $file, bool $isFixer): void
    {
        $old = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($old);
        $oldHash = $tokens->getCodeHash();

        $newHash = $oldHash;
        $new = $old;

        $appliedFixers = [];

        foreach ($this->fixers as $fixer) {
            if ($this->skipper->shouldSkipSourceClassAndFile($fixer, $file->getRealPath())) {
                continue;
            }

            if (! $fixer->supports($file) || ! $fixer->isCandidate($tokens)) {
                continue;
            }

            /* @var FixerInterface $fixer */
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
        if ($isFixer && ($oldHash !== $newHash)) {
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

        Tokens::clearCache();
    }

    /**
     * @param FixerInterface[] $fixers
     */
    private function registerFixers(array $fixers): void
    {
        $this->fixers = $fixers;
    }

    private function detectChangedLineFromTokens(Tokens $tokens): int
    {
        $line = 1;
        foreach ($tokens as $token) {
            $line += substr_count($token->getContent(), PHP_EOL);
            if ($token->isChanged()) {
                return $line;
            }
        }

        return 0;
    }

    private function addErrorToErrorMessageCollector(SplFileInfo $file, FixerInterface $fixer, Tokens $tokens): void
    {
        $filePath = str_replace('//', '/', $file->getPathname());

        $this->errorCollector->addErrorMessage(
            $filePath,
            $this->detectChangedLineFromTokens($tokens),
            $this->prepareErrorMessageFromFixer($fixer),
            get_class($fixer),
            true
        );
    }

    private function prepareErrorMessageFromFixer(FixerInterface $fixer): string
    {
        if ($fixer instanceof DefinedFixerInterface) {
            $definition = $fixer->getDefinition();

            return $definition->getSummary();
        }

        return $fixer->getName();
    }
}
