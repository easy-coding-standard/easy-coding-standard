<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\CurrentSniffProvider;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;

final class File extends BaseFile
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
     * @var CurrentSniffProvider
     */
    private $currentSniffProvider;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @param array[] $tokens
     */
    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorCollector $errorCollector,
        bool $isFixer,
        CurrentSniffProvider $currentSniffProvider,
        Skipper $skipper
    ) {
        $this->path = $path;
        $this->tokens = $tokens;
        $this->fixer = $fixer;
        $this->errorCollector = $errorCollector;

        $this->numTokens = count($this->tokens);
        $this->content = file_get_contents($path);
        $this->isFixer = $isFixer;

        $this->eolChar = PHP_EOL;
        $this->currentSniffProvider = $currentSniffProvider;
        $this->skipper = $skipper;
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
     * {@inheritdoc}
     */
    public function addError($error, $stackPtr, $code, $data = [], $severity = 0, $fixable = false): bool
    {
        $fullyQualifiedCode = $this->currentSniffProvider->getSniffClass() . '.' . $code;
        if ($this->skipper->shouldSkipCodeAndFile($fullyQualifiedCode, $this->path)) {
            return false;
        }

        return parent::addError($error, $stackPtr, $code, $data, $severity, $fixable);
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
        $sniffClassOrCode,
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

        $this->errorCollector->addErrorMessage(
            $this->path,
            $line,
            $message,
            $this->resolveFullyQualifiedCode($sniffClassOrCode),
            $isFixable
        );

        return true;
    }

    private function resolveFullyQualifiedCode(string $sniffClassOrCode): string
    {
        if (class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }

        return $this->currentSniffProvider->getSniffClass() . '.' . $sniffClassOrCode;
    }
}
