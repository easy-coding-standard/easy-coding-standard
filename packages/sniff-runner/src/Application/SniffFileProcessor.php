<?php

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
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
     * @var Sniff[][]
     */
    private $tokenListeners = [];
    /**
     * @var Fixer
     */
    private $fixer;
    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    /**
     * @var DifferInterface
     */
    private $differ;
    /**
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var TargetFileInfoResolver
     */
    private $targetFileInfoResolver;
    /**
     * @param Sniff[] $sniffs
     * @param \PHP_CodeSniffer\Fixer $fixer
     * @param \Symplify\EasyCodingStandard\SniffRunner\File\FileFactory $fileFactory
     * @param \Symplify\EasyCodingStandard\Configuration\Configuration $configuration
     * @param \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector
     * @param \PhpCsFixer\Differ\DifferInterface $differ
     * @param \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector $appliedCheckersCollector
     * @param \Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem
     * @param \Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver $targetFileInfoResolver
     */
    public function __construct($fixer, $fileFactory, $configuration, $errorAndDiffCollector, $differ, $appliedCheckersCollector, $smartFileSystem, $targetFileInfoResolver, array $sniffs = [])
    {
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->differ = $differ;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->addCompatibilityLayer();
        foreach ($sniffs as $sniff) {
            $this->addSniff($sniff);
        }
        $this->smartFileSystem = $smartFileSystem;
        $this->targetFileInfoResolver = $targetFileInfoResolver;
    }
    /**
     * @return void
     * @param \PHP_CodeSniffer\Sniffs\Sniff $sniff
     */
    public function addSniff($sniff)
    {
        $this->sniffs[] = $sniff;
        $tokens = $sniff->register();
        foreach ($tokens as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
    }
    /**
     * @return mixed[]
     */
    public function getCheckers()
    {
        return $this->sniffs;
    }
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return string
     */
    public function processFile($smartFileInfo)
    {
        $file = $this->fileFactory->createFromFileInfo($smartFileInfo);
        $this->fixFile($file, $this->fixer, $smartFileInfo, $this->tokenListeners);
        // add diff
        if ($smartFileInfo->getContents() !== $this->fixer->getContents()) {
            $diff = $this->differ->diff($smartFileInfo->getContents(), $this->fixer->getContents());
            $targetFileInfo = $this->targetFileInfoResolver->resolveTargetFileInfo($smartFileInfo);
            $this->errorAndDiffCollector->addDiffForFileInfo($targetFileInfo, $diff, $this->appliedCheckersCollector->getAppliedCheckersPerFileInfo($smartFileInfo));
        }
        // 4. save file content (faster without changes check)
        if ($this->configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($file->getFilename(), $this->fixer->getContents());
        }
        return $this->fixer->getContents();
    }
    /**
     * @return void
     */
    private function addCompatibilityLayer()
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
     * @param Sniff[][] $tokenListeners
     * @return void
     * @param \Symplify\EasyCodingStandard\SniffRunner\ValueObject\File $file
     * @param \PHP_CodeSniffer\Fixer $fixer
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    private function fixFile($file, $fixer, $smartFileInfo, array $tokenListeners)
    {
        $previousContent = $smartFileInfo->getContents();
        $this->fixer->loops = 0;
        do {
            // Only needed once file content has changed.
            $content = $previousContent;
            $file->setContent($content);
            $file->processWithTokenListenersAndFileInfo($tokenListeners, $smartFileInfo);
            // fixed content
            $previousContent = $fixer->getContents();
            ++$this->fixer->loops;
        } while ($previousContent !== $content);
    }
}
