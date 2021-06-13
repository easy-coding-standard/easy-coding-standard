<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Error;

use ECSPrefix20210613\Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Exception\NotSniffNorFixerException;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20210613\Symplify\SmartFileSystem\SmartFileInfo;
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
     * @var \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector
     */
    private $changedFilesDetector;
    /**
     * @var \Symplify\EasyCodingStandard\Error\FileDiffFactory
     */
    private $fileDiffFactory;
    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorFactory
     */
    private $errorFactory;
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    public function __construct(\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Error\FileDiffFactory $fileDiffFactory, \Symplify\EasyCodingStandard\Error\ErrorFactory $errorFactory, \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider)
    {
        $this->changedFilesDetector = $changedFilesDetector;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->errorFactory = $errorFactory;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    /**
     * @param class-string $sourceClass
     * @return void
     */
    public function addErrorMessage(\ECSPrefix20210613\Symplify\SmartFileSystem\SmartFileInfo $fileInfo, int $line, string $message, string $sourceClass)
    {
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
     */
    public function addSystemErrorMessage(\ECSPrefix20210613\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, int $line, string $message)
    {
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        $this->systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($line, $message, $smartFileInfo);
    }
    /**
     * @return CodingStandardError[]
     */
    public function getErrors() : array
    {
        return $this->codingStandardErrors;
    }
    /**
     * @return SystemError[]
     */
    public function getSystemErrors() : array
    {
        return $this->systemErrors;
    }
    /**
     * @param class-string[] $appliedCheckers
     * @return void
     */
    public function addDiffForFileInfo(\ECSPrefix20210613\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, string $diff, array $appliedCheckers)
    {
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        foreach ($appliedCheckers as $appliedChecker) {
            $this->ensureIsFixerOrChecker($appliedChecker);
        }
        $this->fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($smartFileInfo, $diff, $appliedCheckers);
    }
    /**
     * @return FileDiff[]
     */
    public function getFileDiffs() : array
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
     */
    private function ensureIsFixerOrChecker(string $sourceClass)
    {
        // remove dot suffix of "."
        if (\strpos($sourceClass, '.') !== \false) {
            $sourceClass = (string) \ECSPrefix20210613\Nette\Utils\Strings::before($sourceClass, '.', 1);
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
