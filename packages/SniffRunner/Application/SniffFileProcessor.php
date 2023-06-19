<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use ECSPrefix202306\Nette\Utils\FileSystem;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix202306\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\SniffFileProcessorTest
 */
final class SniffFileProcessor implements FileProcessorInterface
{
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
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;
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
     * @var Sniff[]
     */
    private $sniffs = [];
    /**
     * @var array<int|string, Sniff[]>
     */
    private $tokenListeners = [];
    /**
     * @param RewindableGenerator<Sniff> $sniffs
     */
    public function __construct(Fixer $fixer, FileFactory $fileFactory, DifferInterface $differ, SniffMetadataCollector $sniffMetadataCollector, \ECSPrefix202306\Symfony\Component\Filesystem\Filesystem $filesystem, FileDiffFactory $fileDiffFactory, PrivatesAccessor $privatesAccessor, iterable $sniffs)
    {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->differ = $differ;
        $this->sniffMetadataCollector = $sniffMetadataCollector;
        $this->filesystem = $filesystem;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->privatesAccessor = $privatesAccessor;
        $this->addCompatibilityLayer();
        $sniffs = \iterator_to_array($sniffs->getIterator());
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
    public function processFile(string $filePath, Configuration $configuration) : array
    {
        $this->sniffMetadataCollector->reset();
        $errorsAndDiffs = [];
        $file = $this->fileFactory->createFromFile($filePath);
        $reportSniffClassesWarnings = $configuration->getReportSniffClassesWarnings();
        $this->fixFile($file, $this->fixer, $filePath, $this->tokenListeners, $reportSniffClassesWarnings);
        // add coding standard errors
        $codingStandardErrors = $this->sniffMetadataCollector->getCodingStandardErrors();
        if ($codingStandardErrors !== []) {
            $errorsAndDiffs[Bridge::CODING_STANDARD_ERRORS] = $codingStandardErrors;
        }
        $fileContents = FileSystem::read($filePath);
        // add diff
        if ($fileContents !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($fileContents, $this->fixer->getContents());
            $appliedCheckers = $this->sniffMetadataCollector->getAppliedSniffs();
            $fileDiff = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($filePath, $diff, $appliedCheckers);
            $errorsAndDiffs[Bridge::FILE_DIFFS][] = $fileDiff;
        }
        if ($configuration->isFixer()) {
            $this->filesystem->dumpFile($file->getFilename(), $this->fixer->getContents());
        }
        return $errorsAndDiffs;
    }
    /**
     * For tests or printing contenet
     */
    public function processFileToString(string $filePath) : string
    {
        $file = $this->fileFactory->createFromFile($filePath);
        $this->fixFile($file, $this->fixer, $filePath, $this->tokenListeners, []);
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
    private function fixFile(File $file, Fixer $fixer, string $filePath, array $tokenListeners, array $reportSniffClassesWarnings) : void
    {
        $previousContent = FileSystem::read($filePath);
        $this->fixer->loops = 0;
        do {
            // Only needed once file content has changed.
            $content = $previousContent;
            $this->privatesAccessor->setPrivateProperty($fixer, 'inConflict', \false);
            $file->setContent($content);
            $file->processWithTokenListenersAndFilePath($tokenListeners, $filePath, $reportSniffClassesWarnings);
            // fixed content
            $previousContent = $fixer->getContents();
            ++$this->fixer->loops;
        } while ($previousContent !== $content);
    }
}
