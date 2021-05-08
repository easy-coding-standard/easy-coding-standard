<?php

namespace Symplify\EasyCodingStandard\Error;

use ECSPrefix20210508\Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Exception\NotSniffNorFixerException;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\SmartFileSystem\SmartFileInfo;
final class ErrorAndDiffCollector
{
    /**
     * @var CodingStandardError[]
     */
    private $codingStandardErrors = [];
    /**
     * @var SystemError[]
     */
    private $systemErrors = [];
    /**
     * @var FileDiff[]
     */
    private $fileDiffs = [];
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;
    /**
     * @var FileDiffFactory
     */
    private $fileDiffFactory;
    /**
     * @var ErrorFactory
     */
    private $errorFactory;
    /**
     * @var CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    public function __construct(\Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Error\FileDiffFactory $fileDiffFactory, \Symplify\EasyCodingStandard\Error\ErrorFactory $errorFactory, \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider)
    {
        $this->changedFilesDetector = $changedFilesDetector;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->errorFactory = $errorFactory;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    /**
     * @param class-string $sourceClass
     * @return void
     * @param int $line
     * @param string $message
     */
    public function addErrorMessage(\Symplify\SmartFileSystem\SmartFileInfo $fileInfo, $line, $message, $sourceClass)
    {
        if (\is_object($sourceClass)) {
            $sourceClass = (string) $sourceClass;
        }
        if (\is_object($message)) {
            $message = (string) $message;
        }
        if ($this->currentParentFileInfoProvider->provide() !== null) {
            // skip sniff errors
            return;
        }
        $this->ensureIsFixerOrChecker($sourceClass);
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);
        $codingStandardError = $this->errorFactory->create($line, $message, $sourceClass, $fileInfo);
        $this->codingStandardErrors[] = $codingStandardError;
    }
    /**
     * @return void
     * @param int $line
     * @param string $message
     */
    public function addSystemErrorMessage(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $line, $message)
    {
        if (\is_object($message)) {
            $message = (string) $message;
        }
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        $this->systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($line, $message, $smartFileInfo);
    }
    /**
     * @return mixed[]
     */
    public function getErrors()
    {
        return $this->codingStandardErrors;
    }
    /**
     * @return mixed[]
     */
    public function getSystemErrors()
    {
        return $this->systemErrors;
    }
    /**
     * @param class-string[] $appliedCheckers
     * @return void
     * @param string $diff
     */
    public function addDiffForFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $diff, array $appliedCheckers)
    {
        if (\is_object($diff)) {
            $diff = (string) $diff;
        }
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        foreach ($appliedCheckers as $appliedChecker) {
            $this->ensureIsFixerOrChecker($appliedChecker);
        }
        $this->fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($smartFileInfo, $diff, $appliedCheckers);
    }
    /**
     * @return mixed[]
     */
    public function getFileDiffs()
    {
        return $this->fileDiffs;
    }
    /**
     * Used by external sniff/fixer testing classes
     * @return void
     */
    public function resetCounters()
    {
        $this->codingStandardErrors = [];
        $this->fileDiffs = [];
    }
    /**
     * @return void
     * @param string $sourceClass
     */
    private function ensureIsFixerOrChecker($sourceClass)
    {
        if (\is_object($sourceClass)) {
            $sourceClass = (string) $sourceClass;
        }
        // remove dot suffix of "."
        if (\ECSPrefix20210508\Nette\Utils\Strings::contains($sourceClass, '.')) {
            $sourceClass = (string) \ECSPrefix20210508\Nette\Utils\Strings::before($sourceClass, '.', 1);
        }
        if (\is_a($sourceClass, \PhpCsFixer\Fixer\FixerInterface::class, \true)) {
            return;
        }
        if (\is_a($sourceClass, \PHP_CodeSniffer\Sniffs\Sniff::class, \true)) {
            return;
        }
        $message = \sprintf('Source class "%s" must be "%s" or "%s"', $sourceClass, \PhpCsFixer\Fixer\FixerInterface::class, \PHP_CodeSniffer\Sniffs\Sniff::class);
        throw new \Symplify\EasyCodingStandard\Exception\NotSniffNorFixerException($message);
    }
}
