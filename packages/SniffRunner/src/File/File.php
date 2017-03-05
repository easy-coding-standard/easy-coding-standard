<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Contract\File\FileInterface;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;

final class File extends BaseFile implements FileInterface
{
    /**
     * @var string
     */
    public $tokenizerType = 'PHP';

    /**
     * @var Fixer
     */
    public $fixer;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var bool
     */
    private $isFixer;

    /**
     * @param string $path
     * @param array[] $tokens
     * @param Fixer $fixer
     * @param ErrorCollector $errorCollector
     * @param bool $isFixer
     */
    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorCollector $errorCollector,
        bool $isFixer
    ) {
        $this->path = $path;
        $this->tokens = $tokens;
        $this->fixer = $fixer;
        $this->errorCollector = $errorCollector;

        $this->numTokens = count($this->tokens);
        $this->content = file_get_contents($path);
        $this->isFixer = $isFixer;

        $this->eolChar = PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(): void
    {
        throw new NotImplementedException(sprintf(
            'Method %s not needed to be public. File is already parsed on __construct.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function process(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use external processing.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCount(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorCollector::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorCollector::class
        ));
    }

    /**
     * Delegate to addError().
     *
     * {@inheritdoc}
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0): bool
    {
        $this->addError($error, $stackPtr, $code, $data, $severity, true);

        return $this->isFixer;
    }

    /**
     * Delegated from addError().
     *
     * {@inheritdoc}
     */
    protected function addMessage(
        $isError,
        $message,
        $line,
        $column,
        $sniffClass,
        $data,
        $severity,
        $isFixable = false
    ): bool {
        if (! $isError) { // skip warnings
            return false;
        }

        if (count($data)) {
            $message = vsprintf($message, $data);
        }

        $sniffClass = $this->normalizeSniffClass($sniffClass);

        $this->errorCollector->addErrorMessage(
            $this->path, $line, $message, $sniffClass, $isFixable
        );

        return true;
    }

    private function normalizeSniffClass(string $sourceClass): string
    {
        if (class_exists($sourceClass, false)) {
            return $sourceClass;
        }

        $trace = debug_backtrace(0, 6);

        if ($this->isSniffClass($trace[3]['class'])) {
            return $trace[3]['class'];
        }

        if ($this->isSniffClass($trace[5]['class'])) {
            return $trace[5]['class'];
        }

        return $trace[4]['class'];
    }

    private function isSniffClass(string $class): bool
    {
        return is_a($class, Sniff::class, true);
    }
}
