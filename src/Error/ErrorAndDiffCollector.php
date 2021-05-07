<?php

namespace Symplify\EasyCodingStandard\Error;

use ECSPrefix20210507\Nette\Utils\Strings;
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
    /**
     * @param \Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector $changedFilesDetector
     * @param \Symplify\EasyCodingStandard\Error\FileDiffFactory $fileDiffFactory
     * @param \Symplify\EasyCodingStandard\Error\ErrorFactory $errorFactory
     * @param \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider
     */
    public function __construct($changedFilesDetector, $fileDiffFactory, $errorFactory, $currentParentFileInfoProvider)
    {
        $this->changedFilesDetector = $changedFilesDetector;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->errorFactory = $errorFactory;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    /**
     * @param class-string $sourceClass
     * @return void
     * @param \Symplify\SmartFileSystem\SmartFileInfo $fileInfo
     * @param int $line
     * @param string $message
     */
    public function addErrorMessage($fileInfo, $line, $message, $sourceClass)
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
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param int $line
     * @param string $message
     */
    public function addSystemErrorMessage($smartFileInfo, $line, $message)
    {
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        $this->systemErrors[] = new SystemError($line, $message, $smartFileInfo);
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
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param string $diff
     */
    public function addDiffForFileInfo($smartFileInfo, $diff, array $appliedCheckers)
    {
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
        // remove dot suffix of "."
        if (Strings::contains($sourceClass, '.')) {
            $sourceClass = (string) Strings::before($sourceClass, '.', 1);
        }
        if (\is_a($sourceClass, FixerInterface::class, \true)) {
            return;
        }
        if (\is_a($sourceClass, Sniff::class, \true)) {
            return;
        }
        $message = \sprintf('Source class "%s" must be "%s" or "%s"', $sourceClass, FixerInterface::class, Sniff::class);
        throw new NotSniffNorFixerException($message);
    }
}
