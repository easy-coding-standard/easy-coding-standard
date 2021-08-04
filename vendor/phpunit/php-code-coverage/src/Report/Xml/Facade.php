<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use function count;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function is_dir;
use function is_file;
use function is_writable;
use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use function sprintf;
use function strlen;
use function substr;
use DateTimeImmutable;
use DOMDocument;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\CodeCoverage;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Directory as DirectoryUtil;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\PathExistsButIsNotDirectoryException;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\AbstractNode;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\File as FileNode;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Version;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\XmlException;
use ECSPrefix20210804\SebastianBergmann\Environment\Runtime;
final class Facade
{
    /**
     * @var string
     */
    private $target;
    /**
     * @var Project
     */
    private $project;
    /**
     * @var string
     */
    private $phpUnitVersion;
    public function __construct(string $version)
    {
        $this->phpUnitVersion = $version;
    }
    /**
     * @throws XmlException
     */
    public function process(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\CodeCoverage $coverage, string $target) : void
    {
        if (\substr($target, -1, 1) !== \DIRECTORY_SEPARATOR) {
            $target .= \DIRECTORY_SEPARATOR;
        }
        $this->target = $target;
        $this->initTargetDirectory($target);
        $report = $coverage->getReport();
        $this->project = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Project($coverage->getReport()->name());
        $this->setBuildInformation();
        $this->processTests($coverage->getTests());
        $this->processDirectory($report, $this->project);
        $this->saveDocument($this->project->asDom(), 'index');
    }
    private function setBuildInformation() : void
    {
        $buildNode = $this->project->buildInformation();
        $buildNode->setRuntimeInformation(new \ECSPrefix20210804\SebastianBergmann\Environment\Runtime());
        $buildNode->setBuildTime(new \DateTimeImmutable());
        $buildNode->setGeneratorVersions($this->phpUnitVersion, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Version::id());
    }
    /**
     * @throws PathExistsButIsNotDirectoryException
     * @throws WriteOperationFailedException
     */
    private function initTargetDirectory(string $directory) : void
    {
        if (\is_file($directory)) {
            if (!\is_dir($directory)) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\PathExistsButIsNotDirectoryException($directory);
            }
            if (!\is_writable($directory)) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException($directory);
            }
        }
        \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Directory::create($directory);
    }
    /**
     * @throws XmlException
     */
    private function processDirectory(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory $directory, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Node $context) : void
    {
        $directoryName = $directory->name();
        if ($this->project->projectSourceDirectory() === $directoryName) {
            $directoryName = '/';
        }
        $directoryObject = $context->addDirectory($directoryName);
        $this->setTotals($directory, $directoryObject->totals());
        foreach ($directory->directories() as $node) {
            $this->processDirectory($node, $directoryObject);
        }
        foreach ($directory->files() as $node) {
            $this->processFile($node, $directoryObject);
        }
    }
    /**
     * @throws XmlException
     */
    private function processFile(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\File $file, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Directory $context) : void
    {
        $fileObject = $context->addFile($file->name(), $file->id() . '.xml');
        $this->setTotals($file, $fileObject->totals());
        $path = \substr($file->pathAsString(), \strlen($this->project->projectSourceDirectory()));
        $fileReport = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Report($path);
        $this->setTotals($file, $fileReport->totals());
        foreach ($file->classesAndTraits() as $unit) {
            $this->processUnit($unit, $fileReport);
        }
        foreach ($file->functions() as $function) {
            $this->processFunction($function, $fileReport);
        }
        foreach ($file->lineCoverageData() as $line => $tests) {
            if (!\is_array($tests) || \count($tests) === 0) {
                continue;
            }
            $coverage = $fileReport->lineCoverage((string) $line);
            foreach ($tests as $test) {
                $coverage->addTest($test);
            }
            $coverage->finalize();
        }
        $fileReport->source()->setSourceCode(\file_get_contents($file->pathAsString()));
        $this->saveDocument($fileReport->asDom(), $file->id());
    }
    private function processUnit(array $unit, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Report $report) : void
    {
        if (isset($unit['className'])) {
            $unitObject = $report->classObject($unit['className']);
        } else {
            $unitObject = $report->traitObject($unit['traitName']);
        }
        $unitObject->setLines($unit['startLine'], $unit['executableLines'], $unit['executedLines']);
        $unitObject->setCrap((float) $unit['crap']);
        $unitObject->setNamespace($unit['namespace']);
        foreach ($unit['methods'] as $method) {
            $methodObject = $unitObject->addMethod($method['methodName']);
            $methodObject->setSignature($method['signature']);
            $methodObject->setLines((string) $method['startLine'], (string) $method['endLine']);
            $methodObject->setCrap($method['crap']);
            $methodObject->setTotals((string) $method['executableLines'], (string) $method['executedLines'], (string) $method['coverage']);
        }
    }
    private function processFunction(array $function, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Report $report) : void
    {
        $functionObject = $report->functionObject($function['functionName']);
        $functionObject->setSignature($function['signature']);
        $functionObject->setLines((string) $function['startLine']);
        $functionObject->setCrap($function['crap']);
        $functionObject->setTotals((string) $function['executableLines'], (string) $function['executedLines'], (string) $function['coverage']);
    }
    private function processTests(array $tests) : void
    {
        $testsObject = $this->project->tests();
        foreach ($tests as $test => $result) {
            $testsObject->addTest($test, $result);
        }
    }
    private function setTotals(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\AbstractNode $node, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Xml\Totals $totals) : void
    {
        $loc = $node->linesOfCode();
        $totals->setNumLines($loc->linesOfCode(), $loc->commentLinesOfCode(), $loc->nonCommentLinesOfCode(), $node->numberOfExecutableLines(), $node->numberOfExecutedLines());
        $totals->setNumClasses($node->numberOfClasses(), $node->numberOfTestedClasses());
        $totals->setNumTraits($node->numberOfTraits(), $node->numberOfTestedTraits());
        $totals->setNumMethods($node->numberOfMethods(), $node->numberOfTestedMethods());
        $totals->setNumFunctions($node->numberOfFunctions(), $node->numberOfTestedFunctions());
    }
    private function targetDirectory() : string
    {
        return $this->target;
    }
    /**
     * @throws XmlException
     */
    private function saveDocument(\DOMDocument $document, string $name) : void
    {
        $filename = \sprintf('%s/%s.xml', $this->targetDirectory(), $name);
        $document->formatOutput = \true;
        $document->preserveWhiteSpace = \false;
        $this->initTargetDirectory(\dirname($filename));
        \file_put_contents($filename, $this->documentAsString($document));
    }
    /**
     * @throws XmlException
     *
     * @see https://bugs.php.net/bug.php?id=79191
     */
    private function documentAsString(\DOMDocument $document) : string
    {
        $xmlErrorHandling = \libxml_use_internal_errors(\true);
        $xml = $document->saveXML();
        if ($xml === \false) {
            $message = 'Unable to generate the XML';
            foreach (\libxml_get_errors() as $error) {
                $message .= \PHP_EOL . $error->message;
            }
            throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\XmlException($message);
        }
        \libxml_clear_errors();
        \libxml_use_internal_errors($xmlErrorHandling);
        return $xml;
    }
}
