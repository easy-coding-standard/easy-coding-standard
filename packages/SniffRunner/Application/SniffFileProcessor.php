<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\SniffFileProcessorTest
 */
final class SniffFileProcessor implements \Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface
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
     * @var \PHP_CodeSniffer\Fixer
     */
    private $fixer;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\File\FileFactory
     */
    private $fileFactory;
    /**
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector
     */
    private $sniffMetadataCollector;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symplify\EasyCodingStandard\Error\FileDiffFactory
     */
    private $fileDiffFactory;
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesAccessor
     */
    private $privatesAccessor;
    /**
     * @param Sniff[] $sniffs
     */
    public function __construct(\PHP_CodeSniffer\Fixer $fixer, \Symplify\EasyCodingStandard\SniffRunner\File\FileFactory $fileFactory, \PhpCsFixer\Differ\DifferInterface $differ, \Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector $sniffMetadataCollector, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \Symplify\EasyCodingStandard\Error\FileDiffFactory $fileDiffFactory, \ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor $privatesAccessor, array $sniffs)
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
    public function addSniff(\PHP_CodeSniffer\Sniffs\Sniff $sniff) : void
    {
        $this->sniffs[] = $sniff;
        $tokens = $sniff->register();
        foreach ($tokens as $token) {
            $this->tokenListeners[$token][] = $sniff;
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
     * @return array<string, array<FileDiff|CodingStandardError>>
     */
    public function processFile(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : array
    {
        $this->sniffMetadataCollector->reset();
        $errorsAndDiffs = [];
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);
        $this->fixFile($file, $this->fixer, $smartFileInfo, $this->tokenListeners);
        // add coding standard errors
        $codingStandardErrors = $this->sniffMetadataCollector->getCodingStandardErrors();
        if ($codingStandardErrors !== []) {
            $errorsAndDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] = $codingStandardErrors;
        }
        // add diff
        if ($smartFileInfo->getContents() !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($smartFileInfo->getContents(), $this->fixer->getContents());
            $appliedCheckers = $this->sniffMetadataCollector->getAppliedSniffs();
            $fileDiff = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($smartFileInfo, $diff, $appliedCheckers);
            $errorsAndDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS][] = $fileDiff;
        }
        if ($configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($file->getFilename(), $this->fixer->getContents());
        }
        return $errorsAndDiffs;
    }
    /**
     * For tests or printing contenet
     */
    public function processFileToString(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string
    {
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);
        $this->fixFile($file, $this->fixer, $smartFileInfo, $this->tokenListeners);
        return $this->fixer->getContents();
    }
    private function addCompatibilityLayer() : void
    {
        if (!\defined('PHP_CODESNIFFER_VERBOSITY')) {
            // initalize token with INT type, otherwise php-cs-fixer and php-parser breaks
            if (!\defined('T_MATCH')) {
                \define('T_MATCH', 5000);
            }
            \define('PHP_CODESNIFFER_VERBOSITY', 0);
            new \PHP_CodeSniffer\Util\Tokens();
        }
    }
    /**
     * Mimics @see \PHP_CodeSniffer\Files\File::process()
     *
     * @see \PHP_CodeSniffer\Fixer::fixFile()
     *
     * @param array<int|string, Sniff[]> $tokenListeners
     */
    private function fixFile(\Symplify\EasyCodingStandard\SniffRunner\ValueObject\File $file, \PHP_CodeSniffer\Fixer $fixer, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, array $tokenListeners) : void
    {
        $previousContent = $smartFileInfo->getContents();
        $this->fixer->loops = 0;
        do {
            // Only needed once file content has changed.
            $content = $previousContent;
            $this->privatesAccessor->setPrivateProperty($fixer, 'inConflict', \false);
            $file->setContent($content);
            $file->processWithTokenListenersAndFileInfo($tokenListeners, $smartFileInfo);
            // fixed content
            $previousContent = $fixer->getContents();
            ++$this->fixer->loops;
        } while ($previousContent !== $content);
    }
}
