<?php

namespace Symplify\EasyCodingStandard\SniffRunner\ValueObject;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Util\Common;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use ECSPrefix20210516\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject\FileTest
 */
final class File extends \PHP_CodeSniffer\Files\File
{
    /**
     * Explicit list for classes that use only warnings. ECS only knows only errors, so this one promotes them to error.
     *
     * @var array<class-string<Sniff>>
     */
    const REPORT_WARNINGS_SNIFFS = [\PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff::class];
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
    /**
     * @param string $path
     * @param string $content
     */
    public function __construct($path, $content, \PHP_CodeSniffer\Fixer $fixer, \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector, \ECSPrefix20210516\Symplify\Skipper\Skipper\Skipper $skipper, \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector $appliedCheckersCollector, \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $path = (string) $path;
        $content = (string) $content;
        $this->path = $path;
        $this->content = $content;
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->eolChar = \PHP_CodeSniffer\Util\Common::detectLineEndings($content);
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        // compat
        if (!\defined('PHP_CODESNIFFER_CBF')) {
            \define('PHP_CODESNIFFER_CBF', \false);
        }
        // parent required
        $this->config = new \PHP_CodeSniffer\Config([], \false);
        $this->config->tabWidth = 4;
        $this->config->annotations = \false;
        $this->config->encoding = 'UTF-8';
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    /**
     * Mimics @see
     * https://github.com/squizlabs/PHP_CodeSniffer/blob/e4da24f399d71d1077f93114a72e305286020415/src/Files/File.php#L310
     * @return void
     */
    public function process()
    {
        $this->parse();
        $this->fixer->startFile($this);
        foreach ($this->tokens as $stackPtr => $token) {
            if (!isset($this->tokenListeners[$token['code']])) {
                continue;
            }
            foreach ($this->tokenListeners[$token['code']] as $sniff) {
                if ($this->skipper->shouldSkipElementAndFileInfo($sniff, $this->fileInfo)) {
                    continue;
                }
                $this->reportActiveSniffClass($sniff);
                $sniff->process($this, $stackPtr);
            }
        }
        $this->fixedCount += $this->fixer->getFixCount();
    }
    /**
     * @return void
     */
    public function getErrorCount()
    {
        throw new \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException(\sprintf('Method "%s" is not needed to be public. Use "%s" service.', __METHOD__, \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector::class));
    }
    /**
     * @return mixed[]
     */
    public function getErrors()
    {
        throw new \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException(\sprintf('Method "%s" is not needed to be public. Use "%s" service.', __METHOD__, \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector::class));
    }
    /**
     * Delegate to addError().
     *
     * {@inheritdoc}
     * @return bool
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0)
    {
        $this->appliedCheckersCollector->addFileInfoAndChecker($this->fileInfo, $this->resolveFullyQualifiedCode($code));
        return !$this->shouldSkipError($error, $code, $data);
    }
    /**
     * @return bool
     */
    public function addError($error, $stackPtr, $code, $data = [], $severity = 0, $fixable = \false)
    {
        if ($this->shouldSkipError($error, $code, $data)) {
            return \false;
        }
        return parent::addError($error, $stackPtr, $code, $data, $severity, $fixable);
    }
    /**
     * Allow only specific classes
     *
     * {@inheritdoc}
     * @return bool
     */
    public function addWarning($warning, $stackPtr, $code, $data = [], $severity = 0, $fixable = \false)
    {
        if (!$this->isSniffClassWarningAllowed($this->activeSniffClass)) {
            return \false;
        }
        return $this->addError($warning, $stackPtr, $code, $data, $severity, $fixable);
    }
    /**
     * @param Sniff[][] $tokenListeners
     * @return void
     */
    public function processWithTokenListenersAndFileInfo(array $tokenListeners, \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        $this->tokenListeners = $tokenListeners;
        $this->fileInfo = $fileInfo;
        $this->process();
    }
    /**
     * Delegated from addError().
     *
     * {@inheritdoc}
     * @return bool
     */
    protected function addMessage($isError, $message, $line, $column, $sniffClassOrCode, $data, $severity, $isFixable = \false)
    {
        // skip warnings
        if (!$isError) {
            return \false;
        }
        $message = $data !== [] ? \vsprintf($message, $data) : $message;
        if ($isFixable) {
            return $isFixable;
        }
        // do not add non-fixable errors twice
        if ($this->fixer->loops > 0) {
            return \false;
        }
        $this->errorAndDiffCollector->addErrorMessage($this->fileInfo, $line, $message, $this->resolveFullyQualifiedCode($sniffClassOrCode));
        return \true;
    }
    /**
     * @return void
     */
    private function reportActiveSniffClass(\PHP_CodeSniffer\Sniffs\Sniff $sniff)
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
    /**
     * @param string $sniffClassOrCode
     * @return string
     */
    private function resolveFullyQualifiedCode($sniffClassOrCode)
    {
        $sniffClassOrCode = (string) $sniffClassOrCode;
        if (\class_exists($sniffClassOrCode)) {
            return $sniffClassOrCode;
        }
        return $this->activeSniffClass . '.' . $sniffClassOrCode;
    }
    /**
     * @param string[] $data
     * @param string $error
     * @param string $code
     * @return bool
     */
    private function shouldSkipError($error, $code, array $data)
    {
        $error = (string) $error;
        $code = (string) $code;
        $fullyQualifiedCode = $this->resolveFullyQualifiedCode($code);
        if ($this->skipper->shouldSkipElementAndFileInfo($fullyQualifiedCode, $this->fileInfo)) {
            return \true;
        }
        $message = $data !== [] ? \vsprintf($error, $data) : $error;
        return $this->skipper->shouldSkipElementAndFileInfo($message, $this->fileInfo);
    }
    /**
     * @param string $sniffClass
     * @return bool
     */
    private function isSniffClassWarningAllowed($sniffClass)
    {
        $sniffClass = (string) $sniffClass;
        foreach (self::REPORT_WARNINGS_SNIFFS as $reportWarningsSniff) {
            if (\is_a($sniffClass, $reportWarningsSniff, \true)) {
                return \true;
            }
        }
        return \false;
    }
}
