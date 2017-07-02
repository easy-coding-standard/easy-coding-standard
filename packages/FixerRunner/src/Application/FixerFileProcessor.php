<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Skipper;

final class FixerFileProcessor implements FileProcessorInterface
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
     * @var Configuration
     */
    private $configuration;

    public function __construct(ErrorCollector $errorCollector, Skipper $skipper, Configuration $configuration)
    {
        $this->errorCollector = $errorCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
    }

    public function addFixer(FixerInterface $fixer): void
    {
        $this->fixers[] = $fixer;
    }

    /**
     * @return FixerInterface[]
     */
    public function getFixers(): array
    {
        return $this->fixers;
    }

    public function processFile(SplFileInfo $file): void
    {
        $old = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($old);
        $oldHash = $tokens->getCodeHash();

        $newHash = $oldHash;
        $new = $old;

        $appliedFixers = [];

        foreach ($this->fixers as $fixer) {
            if ($this->skipper->shouldSkipCheckerAndFile($fixer, $file->getRealPath())) {
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
        if ($this->configuration->isFixer() && ($oldHash !== $newHash)) {
            if (@file_put_contents($file->getRealPath(), $new) === false) {
                // @todo: move to sniffer FixerFileProcessor as well, decouple FileSystem service?
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

    private function detectChangedLineFromTokens(Tokens $tokens): int
    {
        $line = 0;
        foreach ($tokens as $token) {
            $line += substr_count($token->getContent(), PHP_EOL);
            if ($token->isChanged()) {
                return ++$line;
            }
        }

        return $line;
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
