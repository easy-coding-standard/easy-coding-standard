<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\ValueObject;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff;
use PHP_CodeSniffer\Util\Common;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use ECSPrefix202206\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject\FileTest
 */
final class File extends BaseFile
{
    /**
     * Explicit list for classes that use only warnings. ECS only knows only errors, so this one promotes them to error.
     *
     * @var array<class-string<Sniff>>
     */
    private const REPORT_WARNINGS_SNIFFS = ['\\PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\CodeAnalysis\\AssignmentInConditionSniff', PropertyDeclarationSniff::class, MethodDeclarationSniff::class, CommentedOutCodeSniff::class];
    /**
     * @var string
     */
    public $tokenizerType = 'PHP';
    /**
     * @var string|null
     */
    private $activeSniffClass = null;
    /**
     * @var string|null
     */
    private $previousActiveSniffClass = null;
    /**
     * @var Sniff[][]
     */
    private $tokenListeners = [];
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $fileInfo;
    /**
     * @var \Symplify\Skipper\Skipper\Skipper
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
        $currentFileInfo = $this->fileInfo;
        if (!$currentFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }
        foreach ($this->tokens as $stackPtr => $token) {
            if (!isset($this->tokenListeners[$token['code']])) {
                continue;
            }
            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipElementAndFileInfo($sniff, $currentFileInfo)) {
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
        if (!$this->isSniffClassWarningAllowed($this->activeSniffClass)) {
            return \false;
        }
        return $this->addError($warning, $stackPtr, $code, $data, $severity, $fixable);
    }
    /**
     * @param array<int|string, Sniff[]> $tokenListeners
     */
    public function processWithTokenListenersAndFileInfo(array $tokenListeners, SmartFileInfo $fileInfo) : void
    {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
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
        if ($this->fileInfo === null) {
            throw new ShouldNotHappenException();
        }
        if ($this->skipper->shouldSkipElementAndFileInfo($fullyQualifiedCode, $this->fileInfo)) {
            return \true;
        }
        $message = $data !== [] ? \vsprintf($error, $data) : $error;
        return $this->skipper->shouldSkipElementAndFileInfo($message, $this->fileInfo);
    }
    private function isSniffClassWarningAllowed(string $sniffClass) : bool
    {
        foreach (self::REPORT_WARNINGS_SNIFFS as $reportWarningsSniff) {
            if (\is_a($sniffClass, $reportWarningsSniff, \true)) {
                return \true;
            }
        }
        return \false;
    }
}
