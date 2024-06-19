<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\ValueObject;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Exception\ShouldNotHappenException;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;

/**
 * @api
 * @see \Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject\FileTest
 */
final class File extends BaseFile
{
    /**
     * @var string
     */
    public $tokenizerType = 'PHP';

    /**
     * @var class-string<Sniff>
     */
    private string|null $activeSniffClass = null;

    private string|null $previousActiveSniffClass = null;

    /**
     * @var array<int|string, Sniff[]>
     */
    private array $tokenListeners = [];

    private ?string $filePath = null;

    /**
     * @var array<class-string<Sniff>, int> A map of which sniffers have
     *     requested themselves to be disabled, pointing to the index in
     *     the files token stack that they are to be re-enabled.
     */
    private array $disabledSniffers = [];

    /**
     * @var array<class-string<Sniff>>
     */
    private array $escalatedSniffClassesWarnings = [];

    public function __construct(
        string $path,
        string $content,
        Fixer $fixer,
        private Skipper $skipper,
        private SniffMetadataCollector $sniffMetadataCollector,
        private EasyCodingStandardStyle $easyCodingStandardStyle,
        Config $config,
        ?Ruleset $ruleset
    ) {
        $this->path = $path;
        $this->content = $content;

        // this property cannot be promoted as defined in constructor
        $this->fixer = $fixer;

        $this->eolChar = Common::detectLineEndings($content);

        // compat
        if (! defined('PHP_CODESNIFFER_CBF')) {
            define('PHP_CODESNIFFER_CBF', false);
        }

        // parent required
        $this->config = $config;

        // @phpstan-ignore-next-line I promise it's right
        $this->ruleset = $ruleset;
    }

    /**
     * Mimics @see
     * https://github.com/squizlabs/PHP_CodeSniffer/blob/e4da24f399d71d1077f93114a72e305286020415/src/Files/File.php#L310
     */
    public function process(): void
    {
        // Since sniffs are re-run after they do fixes, we need to clear the old
        // errors to avoid duplicates.
        $this->sniffMetadataCollector->resetErrors();

        $this->parse();
        $this->fixer->startFile($this);

        $currentFilePath = $this->filePath;
        if (! is_string($currentFilePath)) {
            throw new ShouldNotHappenException();
        }

        foreach ($this->tokens as $stackPtr => $token) {
            if (! isset($this->tokenListeners[$token['code']])) {
                continue;
            }

            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                $shouldSkipSniff = $this->skipper->shouldSkipElementAndFilePath($sniff, $currentFilePath);
                $sniffIsDisabled = $this->isSniffStillDisabled($sniff::class, $stackPtr);

                if ($shouldSkipSniff || $sniffIsDisabled) {
                    continue;
                }

                $this->reportActiveSniffClass($sniff);
                $this->disableSnifferUntil($sniff::class, $sniff->process($this, $stackPtr));
            }
        }

        $this->fixedCount += $this->fixer->getFixCount();
        $this->disabledSniffers = [];
    }

    /**
     * @param mixed[] $data
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0): bool
    {
        $this->assertIsProcessing();

        $trueSeverity = $this->getTrueCodeSeverity($code, $severity);

        if ($this->shouldSkipError($error, $code, $data, $trueSeverity)) {
            return false;
        }

        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);
        $this->sniffMetadataCollector->addAppliedSniff($fullyQualifiedCode);

        return true;
    }

    /**
     * @param mixed[] $data
     */
    public function addFixableWarning($warning, $stackPtr, $code, $data = [], $severity = 0): bool
    {
        $this->assertIsProcessing();

        $trueSeverity = $this->getTrueCodeSeverity($code, $severity);

        if ($this->shouldSkipWarning($warning, $code, $data, $trueSeverity, $this->activeSniffClass)) {
            return false;
        }

        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);
        $this->sniffMetadataCollector->addAppliedSniff($fullyQualifiedCode);

        return true;
    }

    /**
     * @param mixed[] $data
     */
    public function addError($error, $stackPtr, $code, $data = [], $severity = 0, $fixable = false): bool
    {
        $this->assertIsProcessing();

        $trueSeverity = $this->getTrueCodeSeverity($code, $severity);

        if ($this->shouldSkipError($error, $code, $data, $trueSeverity)) {
            return false;
        }

        return parent::addError($error, $stackPtr, $code, $data, $trueSeverity, $fixable);
    }

    /**
     * @param mixed $data
     * Allow only specific classes
     */
    public function addWarning($warning, $stackPtr, $code, $data = [], $severity = 0, $fixable = false): bool
    {
        $this->assertIsProcessing();

        $trueSeverity = $this->getTrueCodeSeverity($code, $severity);

        if ($this->shouldSkipWarning($warning, $code, $data, $trueSeverity, $this->activeSniffClass)) {
            return false;
        }

        return $this->addError($warning, $stackPtr, $code, $data, $trueSeverity, $fixable);
    }

    /**
     * @param mixed[] $data
     */
    public function addErrorOnLine($error, $line, $code, $data = [], $severity = 0): bool
    {
        $this->assertIsProcessing();

        $trueSeverity = $this->getTrueCodeSeverity($code, $severity);

        if ($this->shouldSkipError($error, $code, $data, $trueSeverity)) {
            return false;
        }

        return parent::addErrorOnLine($error, $line, $code, $data, $trueSeverity);
    }

    /**
     * @param mixed[] $data
     */
    public function addWarningOnLine($warning, $line, $code, $data = [], $severity = 0): bool
    {
        $this->assertIsProcessing();

        $trueSeverity = $this->getTrueCodeSeverity($code, $severity);

        if ($this->shouldSkipWarning($warning, $code, $data, $trueSeverity, $this->activeSniffClass)) {
            return false;
        }

        return parent::addWarningOnLine($warning, $line, $code, $data, $trueSeverity);
    }

    /**
     * @param array<class-string<Sniff>> $escalatedSniffClassesWarnings
     * @param array<int|string, Sniff[]> $tokenListeners
     */
    public function processWithTokenListenersAndFilePath(
        array $tokenListeners,
        string $filePath,
        array $escalatedSniffClassesWarnings
    ): void {
        $this->tokenListeners = $tokenListeners;
        $this->filePath = $filePath;
        $this->escalatedSniffClassesWarnings = $escalatedSniffClassesWarnings;
        $this->process();
    }

    /**
     * @param mixed $data
     * Delegated from addError().
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
        // hardcode skip the PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff.FoundInWhileCondition
        // as the only code is passed and this rule does not make sense
        if ($sniffClassOrCode === 'FoundInWhileCondition') {
            return false;
        }

        $message = $data !== [] ? vsprintf($message, $data) : $message;

        $checkerClass = $this->resolveFullyQualifiedCode($sniffClassOrCode);
        $codingStandardError = new CodingStandardError($line, $message, $checkerClass, $this->getFilename());

        $this->sniffMetadataCollector->addCodingStandardError($codingStandardError);

        if ($isFixable) {
            return $isFixable;
        }

        // do not add non-fixable errors twice
        return $this->fixer->loops === 0;
    }

    private function reportActiveSniffClass(Sniff $sniff): void
    {
        // used in other places later
        $this->activeSniffClass = $sniff::class;

        if (! $this->easyCodingStandardStyle->isDebug()) {
            return;
        }

        if ($this->previousActiveSniffClass === $this->activeSniffClass) {
            return;
        }

        $this->easyCodingStandardStyle->writeln('     [sniff] ' . $this->activeSniffClass);
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
    private function shouldSkipError(string $error, string $code, array $data, int $severity): bool
    {
        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);

        $this->assertIsProcessing();

        if ($this->skipper->shouldSkipElementAndFilePath($fullyQualifiedCode, $this->filePath)) {
            return true;
        }

        if ($this->shouldSkipSeverity($severity, $this->config->errorSeverity)) {
            return true;
        }

        $message = $data !== [] ? vsprintf($error, $data) : $error;

        return $this->skipper->shouldSkipElementAndFilePath($message, $this->filePath);
    }

    /**
     * @param string[] $data
     * @param class-string<Sniff> $sniffClass
     */
    private function shouldSkipWarning(
        string $error,
        string $code,
        array $data,
        int $severity,
        string $sniffClass
    ): bool {
        // I'm not sure why we do this.
        if ($this->shouldEscalateClassWarnings($sniffClass)) {
            return $this->shouldSkipError($error, $code, $data, $severity);
        }

        return $this->shouldSkipSeverity($severity, $this->config->warningSeverity);
    }

    private function shouldEscalateClassWarnings(string $sniffClass): bool
    {
        foreach ($this->escalatedSniffClassesWarnings as $escalatedSniffClassWarning) {
            if (is_a($sniffClass, $escalatedSniffClassWarning, true)) {
                return true;
            }
        }

        return false;
    }

    private function isSniffStillDisabled(string $sniffClass, int $targetStackPtr): bool
    {
        $disabledUntil = $this->disabledSniffers[$sniffClass] ?? 0;

        if ($disabledUntil > $targetStackPtr) {
            return true;
        }

        unset($this->disabledSniffers[$sniffClass]);
        return false;
    }

    /**
     * @param class-string<Sniff> $sniffClass
     */
    private function disableSnifferUntil(string $sniffClass, ?int $targetStackPtr = null): void
    {
        if ($targetStackPtr === null) {
            return;
        }

        $this->disabledSniffers[$sniffClass] = $targetStackPtr;
    }

    /**
     * Most sniffs never report severity information, and during processing
     * PHPCS adds either the default (5) or the configured severity from the
     * ruleset.
     *
     * As well, sniff classes often include multiple codes, which can be enabled
     * individually. It's not enough to just know which fixers should be run.
     *
     * This method allows us to decipher the true severity based, like PHPCS
     * itself does.
     *
     * A code with severity 0 should always be skipped, as it's not included
     * in the ruleset.
     */
    private function getTrueCodeSeverity(string $code, int $reportedSeverity): int
    {
        $this->assertIsProcessing();

        $trueSeverity = $reportedSeverity ?: 5;

        // If we're not using a PHPCS ruleset, codes don't matter.
        if ($this->ruleset === null) {
            return $trueSeverity;
        }

        $codeParts = explode(
            '.',
            str_contains($code, '.')
                ? $code
                : sprintf('%s.%s', Common::getSniffCode($this->activeSniffClass), $code)
        );

        $codeVariants = [
            vsprintf('%s.%s.%s.%s', $codeParts),
            vsprintf('%s.%s.%s', $codeParts),
            vsprintf('%s.%s', $codeParts),
            vsprintf('%s', $codeParts),
        ];

        foreach ($codeVariants as $codeToCheck) {
            $severityFromRules = $this->ruleset->ruleset[$codeToCheck]['severity'] ?? 5;

            if ($severityFromRules === 0) {
                return 0;
            }
        }

        return $trueSeverity;
    }

    /**
     * @see self::getTrueCodeSeverity()
     */
    private function shouldSkipSeverity(int $severity, int $configSeverity): bool
    {
        if ($severity === 0 || $configSeverity === 0) {
            return true;
        }

        return $severity < $configSeverity;
    }

    /**
     * @return never|void
     *
     * @phpstan-assert string $this->filePath
     * @phpstan-assert string $this->activeSniffClass
     */
    private function assertIsProcessing(): void
    {
        if ($this->activeSniffClass === null || ! is_string($this->filePath)) {
            throw new ShouldNotHappenException();
        }
    }
}
