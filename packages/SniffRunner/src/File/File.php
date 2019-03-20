<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff;
use PHP_CodeSniffer\Util\Common;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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
     * @var string|null
     */
    private $activeSniffClass;

    /**
     * @var string|null
     */
    private $previousActiveSniffClass;

    /**
     * Explicit list for now.
     *
     * @var string[]
     */
    private $reportWarningsSniffs = [
        CommentedOutCodeSniff::class,
        AssignmentInConditionSniff::class,
        PropertyDeclarationSniff::class,
        MethodDeclarationSniff::class,
    ];

    /**
     * @var Sniff[][]
     */
    private $tokenListeners = [];

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
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var SmartFileInfo
     */
    private $fileInfo;

    public function __construct(
        string $path,
        string $content,
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->path = $path;
        $this->content = $content;
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;

        $this->eolChar = Common::detectLineEndings($content);
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;

        // compat
        if (! defined('PHP_CODESNIFFER_CBF')) {
            define('PHP_CODESNIFFER_CBF', false);
        }

        // parent required
        $this->config = new Config([], false);
        $this->config->tabWidth = 4;
        $this->config->annotations = false;
        $this->config->encoding = 'UTF-8';
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    /**
     * Mimics @see https://github.com/squizlabs/PHP_CodeSniffer/blob/e4da24f399d71d1077f93114a72e305286020415/src/Files/File.php#L310
     */
    public function process(): void
    {
        $this->parse();
        $this->fixer->startFile($this);

        foreach ($this->tokens as $stackPtr => $token) {
            if (isset($this->tokenListeners[$token['code']]) === false) {
                continue;
            }

            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipCheckerAndFile($sniff, $this->fileInfo)) {
                    continue;
                }

                $this->reportActiveSniffClass($sniff);

                $sniff->process($this, $stackPtr);
            }
        }

        $this->fixedCount += $this->fixer->getFixCount();
    }

    public function getErrorCount(): int
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorAndDiffCollector::class
        ));
    }

    /**
     * @return mixed[]
     */
    public function getErrors(): array
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
            $this->fileInfo,
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
        if (! $this->isSniffClassWarningAllowed($this->activeSniffClass)) {
            return false;
        }

        return $this->addError($warning, $stackPtr, $code, $data, $severity, $fixable);
    }

    /**
     * @param Sniff[][] $tokenListeners
     */
    public function processWithTokenListenersAndFileInfo(array $tokenListeners, SmartFileInfo $fileInfo): void
    {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
        $this->process();
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

        // do not add non-fixable errors twice
        if ($this->fixer->loops > 0) {
            return false;
        }

        $this->errorAndDiffCollector->addErrorMessage(
            $this->fileInfo,
            $line,
            $message,
            $this->resolveFullyQualifiedCode($sniffClassOrCode)
        );

        return true;
    }

    private function reportActiveSniffClass(Sniff $sniff): void
    {
        // used in other places later
        $this->activeSniffClass = get_class($sniff);

        if (! $this->easyCodingStandardStyle->isDebug()) {
            return;
        }

        if ($this->previousActiveSniffClass === $this->activeSniffClass) {
            return;
        }

        $this->easyCodingStandardStyle->writeln($this->activeSniffClass);
        $this->previousActiveSniffClass = $this->activeSniffClass;
    }

    private function resolveFullyQualifiedCode(string $sniffClassOrCode): string
    {
        if (class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }

        return $this->activeSniffClass . '.' . $sniffClassOrCode;
    }

    /**
     * @param string[] $data
     */
    private function shouldSkipError(string $error, string $code, array $data): bool
    {
        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);

        if ($this->skipper->shouldSkipCodeAndFile($fullyQualifiedCode, $this->fileInfo)) {
            return true;
        }

        $message = count($data) ? vsprintf($error, $data) : $error;

        return $this->skipper->shouldSkipMessageAndFile($message, $this->fileInfo);
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
