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

    /**
     * @var bool
     */
    private $isFixer = false;

    public function __construct(ErrorCollector $errorCollector, Skipper $skipper, FixerFactory $fixerFactory)
    {
        $this->errorCollector = $errorCollector;
        $this->skipper = $skipper;
        $this->fixerFactory = $fixerFactory;
    }

    /**
     * @var string $class
     * @var mixed[] $configuration
     */
    public function addFixer(string $class, array $configuration = [])
    {
        dump($class, $configuration);
        die;
    }

    public function setupWithCommand(RunCommand $runCommand): void
    {
        $this->fixers = $this->fixerFactory->createFromClasses($runCommand->getFixers());
        $this->isFixer = $runCommand->isFixer();
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
        if ($this->isFixer && ($oldHash !== $newHash)) {
            if (@file_put_contents($file->getRealPath(), $new) === false) {
                // @todo: move to sniffer FileProcessor as well, decouple FileSystem service?
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
