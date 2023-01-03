<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use ECSPrefix202301\Nette\Utils\FileSystem;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix202301\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ECSPrefix202301\Symplify\SmartFileSystem\SmartFileSystem;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\SniffFileProcessorTest
 */
final class SniffFileProcessor implements FileProcessorInterface
{
    /**
     * @var Sniff[]
     */
    private $sniffs = [];
    /**
     * @var array<int|string, Sniff[]>
     */
    private $tokenListeners = [];
    /**
     * @readonly
     * @var \PHP_CodeSniffer\Fixer
     */
    private $fixer;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SniffRunner\File\FileFactory
     */
    private $fileFactory;
    /**
     * @readonly
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector
     */
    private $sniffMetadataCollector;
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Error\FileDiffFactory
     */
    private $fileDiffFactory;
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Reflection\PrivatesAccessor
     */
    private $privatesAccessor;
    /**
     * @param Sniff[] $sniffs
     */
    public function __construct(Fixer $fixer, FileFactory $fileFactory, DifferInterface $differ, SniffMetadataCollector $sniffMetadataCollector, SmartFileSystem $smartFileSystem, FileDiffFactory $fileDiffFactory, PrivatesAccessor $privatesAccessor, array $sniffs)
    {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->differ = $differ;
        $this->sniffMetadataCollector = $sniffMetadataCollector;
        $this->smartFileSystem = $smartFileSystem;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->privatesAccessor = $privatesAccessor;
        $this->addCompatibilityLayer();
        foreach ($sniffs as $sniff) {
            $this->addSniff($sniff);
        }
    }
    /**
     * @return Sniff[]
     */
    public function getCheckers() : array
    {
        return $this->sniffs;
    }
    /**
     * @return array{file_diffs?: FileDiff[], coding_standard_errors?: CodingStandardError[]}
     */
    public function processFile(SplFileInfo $fileInfo, Configuration $configuration) : array
    {
        $this->sniffMetadataCollector->reset();
        $errorsAndDiffs = [];
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $reportSniffClassesWarnings = $configuration->getReportSniffClassesWarnings();
        $this->fixFile($file, $this->fixer, $fileInfo, $this->tokenListeners, $reportSniffClassesWarnings);
        // add coding standard errors
        $codingStandardErrors = $this->sniffMetadataCollector->getCodingStandardErrors();
        if ($codingStandardErrors !== []) {
            $errorsAndDiffs[Bridge::CODING_STANDARD_ERRORS] = $codingStandardErrors;
        }
        $fileContents = FileSystem::read($fileInfo->getRealPath());
        // add diff
        if ($fileContents !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($fileContents, $this->fixer->getContents());
            $appliedCheckers = $this->sniffMetadataCollector->getAppliedSniffs();
            $fileDiff = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($fileInfo, $diff, $appliedCheckers);
            $errorsAndDiffs[Bridge::FILE_DIFFS][] = $fileDiff;
        }
        if ($configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($file->getFilename(), $this->fixer->getContents());
        }
        return $errorsAndDiffs;
    }
    /**
     * For tests or printing contenet
     *
     * @param \SplFileInfo|string $fileInfo
     */
    public function processFileToString($fileInfo) : string
    {
        if (\is_string($fileInfo)) {
            $fileInfo = new SplFileInfo($fileInfo);
        }
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $this->fixFile($file, $this->fixer, $fileInfo, $this->tokenListeners, []);
        return $this->fixer->getContents();
    }
    private function addSniff(Sniff $sniff) : void
    {
        $this->sniffs[] = $sniff;
        $tokens = $sniff->register();
        foreach ($tokens as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }
    private function addCompatibilityLayer() : void
    {
        if (!\defined('PHP_CODESNIFFER_VERBOSITY')) {
            // initalize token with INT type, otherwise php-cs-fixer and php-parser breaks
            if (!\defined('T_MATCH')) {
                \define('T_MATCH', 5000);
            }
            \define('PHP_CODESNIFFER_VERBOSITY', 0);
            new Tokens();
        }
    }
    /**
     * Mimics @see \PHP_CodeSniffer\Files\File::process()
     *
     * @see \PHP_CodeSniffer\Fixer::fixFile()
     *
     * @param array<int|string, Sniff[]> $tokenListeners
     * @param array<class-string<Sniff>> $reportSniffClassesWarnings
     */
    private function fixFile(File $file, Fixer $fixer, SplFileInfo $fileInfo, array $tokenListeners, array $reportSniffClassesWarnings) : void
    {
        $previousContent = FileSystem::read($fileInfo->getRealPath());
        $this->fixer->loops = 0;
        do {
            // Only needed once file content has changed.
            $content = $previousContent;
            $this->privatesAccessor->setPrivateProperty($fixer, 'inConflict', \false);
            $file->setContent($content);
            $file->processWithTokenListenersAndFileInfo($tokenListeners, $fileInfo, $reportSniffClassesWarnings);
            // fixed content
            $previousContent = $fixer->getContents();
            ++$this->fixer->loops;
        } while ($previousContent !== $content);
    }
}
