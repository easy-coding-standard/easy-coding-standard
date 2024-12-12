<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\ValueObject;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
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
     * @var \Symplify\EasyCodingStandard\Skipper\Skipper\Skipper
     */
    private $skipper;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector
     */
    private $sniffMetadataCollector;
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * @var string|null
     */
    private $activeSniffClass = null;
    /**
     * @var string|null
     */
    private $previousActiveSniffClass = null;
    /**
     * @var array<int|string, Sniff[]>
     */
    private $tokenListeners = [];
    /**
     * @var string|null
     */
    private $filePath;
    /**
     * @var array<class-string<Sniff>>
     */
    private $reportSniffClassesWarnings = [];
    public function __construct(string $path, string $content, Fixer $fixer, Skipper $skipper, SniffMetadataCollector $sniffMetadataCollector, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->skipper = $skipper;
        $this->sniffMetadataCollector = $sniffMetadataCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->path = $path;
        $this->content = $content;
        // this property cannot be promoted as defined in constructor
        $this->fixer = $fixer;
        $this->eolChar = Common::detectLineEndings($content);
        // compat
        if (!\defined('PHP_CODESNIFFER_CBF')) {
            \define('PHP_CODESNIFFER_CBF', \false);
        }
        // parent required
        $this->config = new Config([], \false);
        $this->config->tabWidth = 4;
        $this->config->annotations = \false;
        $this->config->encoding = 'UTF-8';
    }
    /**
     * Mimics @see
     * https://github.com/squizlabs/PHP_CodeSniffer/blob/e4da24f399d71d1077f93114a72e305286020415/src/Files/File.php#L310
     */
    public function process() : void
    {
        $this->parse();
        $this->fixer->startFile($this);
        $currentFilePath = $this->filePath;
        if (!\is_string($currentFilePath)) {
            throw new ShouldNotHappenException();
        }
        foreach ($this->tokens as $stackPtr => $token) {
            if (!isset($this->tokenListeners[$token['code']])) {
                continue;
            }
            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipElementAndFilePath($sniff, $currentFilePath)) {
                    continue;
                }
                $this->reportActiveSniffClass($sniff);
                $sniff->process($this, $stackPtr);
            }
        }
        $this->fixedCount += $this->fixer->getFixCount();
    }
    /**
     * Delegate to addError().
     *
     * @param mixed[] $data
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0) : bool
    {
        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);
        $this->sniffMetadataCollector->addAppliedSniff($fullyQualifiedCode);
        return !$this->shouldSkipError($error, $code, $data);
    }
    /**
     * @param mixed[] $data
     */
    public function addError($error, $stackPtr, $code, $data = [], $severity = 0, $fixable = \false) : bool
    {
        if ($this->shouldSkipError($error, $code, $data)) {
            return \false;
        }
        return parent::addError($error, $stackPtr, $code, $data, $severity, $fixable);
    }
    /**
     * @param mixed $data
     * Allow only specific classes
     */
    public function addWarning($warning, $stackPtr, $code, $data = [], $severity = 0, $fixable = \false) : bool
    {
        if ($this->activeSniffClass === null) {
            throw new ShouldNotHappenException();
        }
        if ($this->shouldSkipClassWarnings($this->activeSniffClass)) {
            return \false;
        }
        return $this->addError($warning, $stackPtr, $code, $data, $severity, $fixable);
    }
    /**
     * @param array<class-string<Sniff>> $reportSniffClassesWarnings
     * @param array<int|string, Sniff[]> $tokenListeners
     */
    public function processWithTokenListenersAndFilePath(array $tokenListeners, string $filePath, array $reportSniffClassesWarnings) : void
    {
        $this->tokenListeners = $tokenListeners;
        $this->filePath = $filePath;
        $this->reportSniffClassesWarnings = $reportSniffClassesWarnings;
        $this->process();
    }
    /**
     * @param mixed $data
     * Delegated from addError().
     */
    protected function addMessage($isError, $message, $line, $column, $sniffClassOrCode, $data, $severity, $isFixable = \false) : bool
    {
        // skip warnings
        if (!$isError) {
            return \false;
        }
        // hardcode skip the PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff.FoundInWhileCondition
        // as the only code is passed and this rule does not make sense
        if ($sniffClassOrCode === 'FoundInWhileCondition') {
            return \false;
        }
        $message = $data !== [] ? \vsprintf($message, $data) : $message;
        $checkerClass = $this->resolveFullyQualifiedCode($sniffClassOrCode);
        $codingStandardError = new CodingStandardError($line, $message, $checkerClass, $this->getFilename());
        $this->sniffMetadataCollector->addCodingStandardError($codingStandardError);
        if ($isFixable) {
            return $isFixable;
        }
        // do not add non-fixable errors twice
        return $this->fixer->loops === 0;
    }
    private function reportActiveSniffClass(Sniff $sniff) : void
    {
        // used in other places later
        $this->activeSniffClass = \get_class($sniff);
        if (!$this->easyCodingStandardStyle->isDebug()) {
            return;
        }
        if ($this->previousActiveSniffClass === $this->activeSniffClass) {
            return;
        }
        $this->easyCodingStandardStyle->writeln('     [sniff] ' . $this->activeSniffClass);
        $this->previousActiveSniffClass = $this->activeSniffClass;
    }
    private function resolveFullyQualifiedCode(string $sniffClassOrCode) : string
    {
        if (\class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }
        return $this->activeSniffClass . '.' . $sniffClassOrCode;
    }
    /**
     * @param string[] $data
     */
    private function shouldSkipError(string $error, string $code, array $data) : bool
    {
        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);
        if (!\is_string($this->filePath)) {
            throw new ShouldNotHappenException();
        }
        if ($this->skipper->shouldSkipElementAndFilePath($fullyQualifiedCode, $this->filePath)) {
            return \true;
        }
        $message = $data !== [] ? \vsprintf($error, $data) : $error;
        return $this->skipper->shouldSkipElementAndFilePath($message, $this->filePath);
    }
    private function shouldSkipClassWarnings(string $sniffClass) : bool
    {
        foreach ($this->reportSniffClassesWarnings as $reportSniffClassWarning) {
            if (\is_a($sniffClass, $reportSniffClassWarning, \true)) {
                return \false;
            }
        }
        return \true;
    }
}
