<?php

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Strings;
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

    public function __construct(
        ChangedFilesDetector $changedFilesDetector,
        FileDiffFactory $fileDiffFactory,
        ErrorFactory $errorFactory,
        CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
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
    public function addErrorMessage(SmartFileInfo $fileInfo, $line, $message, $sourceClass)
    {
        $line = (int) $line;
        $message = (string) $message;
        $sourceClass = (string) $sourceClass;
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
    public function addSystemErrorMessage(SmartFileInfo $smartFileInfo, $line, $message)
    {
        $line = (int) $line;
        $message = (string) $message;
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
     * @param string $diff
     */
    public function addDiffForFileInfo(SmartFileInfo $smartFileInfo, $diff, array $appliedCheckers)
    {
        $diff = (string) $diff;
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);

        foreach ($appliedCheckers as $appliedChecker) {
            $this->ensureIsFixerOrChecker($appliedChecker);
        }

        $this->fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers(
            $smartFileInfo,
            $diff,
            $appliedCheckers
        );
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
        $sourceClass = (string) $sourceClass;
        // remove dot suffix of "."
        if (Strings::contains($sourceClass, '.')) {
            $sourceClass = (string) Strings::before($sourceClass, '.', 1);
        }

        if (is_a($sourceClass, FixerInterface::class, true)) {
            return;
        }

        if (is_a($sourceClass, Sniff::class, true)) {
            return;
        }

        $message = sprintf('Source class "%s" must be "%s" or "%s"', $sourceClass, FixerInterface::class, Sniff::class);
        throw new NotSniffNorFixerException($message);
    }
}
