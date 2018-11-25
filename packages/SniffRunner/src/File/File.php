<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Application\CurrentCheckerProvider;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use function Safe\sprintf;

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
     * Explicit list for now.
     *
     * @var string[]
     */
    private $reportWarningsSniffs = [CommentedOutCodeSniff::class, AssignmentInConditionSniff::class];

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    /**
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    /**
     * @var CurrentCheckerProvider
     */
    private $currentCheckerProvider;

    /**
     * @param array[] $tokens
     */
    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        CurrentCheckerProvider $currentCheckerProvider,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        CurrentFileProvider $currentFileProvider
    ) {
        $this->path = $path;
        $this->tokens = $tokens;
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;

        $this->numTokens = count($this->tokens);

        $this->eolChar = PHP_EOL;
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->currentFileProvider = $currentFileProvider;
        $this->currentCheckerProvider = $currentCheckerProvider;
    }

    public function parse(): void
    {
        throw new NotImplementedException(sprintf(
            'Method %s not needed to be public. File is already parsed on __construct.',
            __METHOD__
        ));
    }

    public function process(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use external processing.',
            __METHOD__
        ));
    }

    public function getErrorCount(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorAndDiffCollector::class
        ));
    }

    public function getErrors(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorAndDiffCollector::class
        ));
    }

    /**
     * Delegate to addError().
     *
     * {@inheritdoc}
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0): bool
    {
        $this->appliedCheckersCollector->addFileInfoAndChecker(
            $this->currentFileProvider->getFileInfo(),
            $this->resolveFullyQualifiedCode($code)
        );

        return ! $this->shouldSkipError($error, $code, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function addError($error, $stackPtr, $code, $data = [], $severity = 0, $fixable = false): bool
    {
        if ($this->shouldSkipError($error, $code, $data)) {
            return false;
        }

        return parent::addError($error, $stackPtr, $code, $data, $severity, $fixable);
    }

    /**
     * Allow only specific classes
     *
     * {@inheritdoc}
     */
    public function addWarning($warning, $stackPtr, $code, $data = [], $severity = 0, $fixable = false): bool
    {
        if (! $this->isSniffClassWarningAllowed($this->currentCheckerProvider->getChecker())) {
            return false;
        }

        return $this->addError($warning, $stackPtr, $code, $data, $severity, $fixable);
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

        $message = count($data) ? vsprintf($message, $data) : $message;

        if ($isFixable === true) {
            return $isFixable;
        }

        $this->errorAndDiffCollector->addErrorMessage(
            $this->currentFileProvider->getFileInfo(),
            $line,
            $message,
            $this->resolveFullyQualifiedCode($sniffClassOrCode)
        );

        return true;
    }

    private function resolveFullyQualifiedCode(string $sniffClassOrCode): string
    {
        if (class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }

        return $this->currentCheckerProvider->getChecker() . '.' . $sniffClassOrCode;
    }

    /**
     * @param string[] $data
     */
    private function shouldSkipError(string $error, string $code, array $data): bool
    {
        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);

        $fileInfo = $this->currentFileProvider->getFileInfo();
        if ($this->skipper->shouldSkipCodeAndFile($fullyQualifiedCode, $fileInfo)) {
            return true;
        }

        $message = count($data) ? vsprintf($error, $data) : $error;

        return $this->skipper->shouldSkipMessageAndFile($message, $fileInfo);
    }

    private function isSniffClassWarningAllowed(string $sniffClass): bool
    {
        foreach ($this->reportWarningsSniffs as $reportWarningsSniff) {
            if (is_a($sniffClass, $reportWarningsSniff, true)) {
                return true;
            }
        }

        return false;
    }
}
