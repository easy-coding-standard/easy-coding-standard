<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage;

use function count;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Filter\DirectoryCollection;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Clover;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Cobertura;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Crap4j;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Xml;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Directory;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class CodeCoverage
{
    /**
     * @var ?Directory
     */
    private $cacheDirectory;
    /**
     * @var DirectoryCollection
     */
    private $directories;
    /**
     * @var FileCollection
     */
    private $files;
    /**
     * @var DirectoryCollection
     */
    private $excludeDirectories;
    /**
     * @var FileCollection
     */
    private $excludeFiles;
    /**
     * @var bool
     */
    private $pathCoverage;
    /**
     * @var bool
     */
    private $includeUncoveredFiles;
    /**
     * @var bool
     */
    private $processUncoveredFiles;
    /**
     * @var bool
     */
    private $ignoreDeprecatedCodeUnits;
    /**
     * @var bool
     */
    private $disableCodeCoverageIgnore;
    /**
     * @var ?Clover
     */
    private $clover;
    /**
     * @var ?Cobertura
     */
    private $cobertura;
    /**
     * @var ?Crap4j
     */
    private $crap4j;
    /**
     * @var ?Html
     */
    private $html;
    /**
     * @var ?Php
     */
    private $php;
    /**
     * @var ?Text
     */
    private $text;
    /**
     * @var ?Xml
     */
    private $xml;
    public function __construct(?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Directory $cacheDirectory, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Filter\DirectoryCollection $directories, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection $files, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Filter\DirectoryCollection $excludeDirectories, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection $excludeFiles, bool $pathCoverage, bool $includeUncoveredFiles, bool $processUncoveredFiles, bool $ignoreDeprecatedCodeUnits, bool $disableCodeCoverageIgnore, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Clover $clover, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Cobertura $cobertura, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Crap4j $crap4j, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html $html, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php $php, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text $text, ?\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Xml $xml)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->directories = $directories;
        $this->files = $files;
        $this->excludeDirectories = $excludeDirectories;
        $this->excludeFiles = $excludeFiles;
        $this->pathCoverage = $pathCoverage;
        $this->includeUncoveredFiles = $includeUncoveredFiles;
        $this->processUncoveredFiles = $processUncoveredFiles;
        $this->ignoreDeprecatedCodeUnits = $ignoreDeprecatedCodeUnits;
        $this->disableCodeCoverageIgnore = $disableCodeCoverageIgnore;
        $this->clover = $clover;
        $this->cobertura = $cobertura;
        $this->crap4j = $crap4j;
        $this->html = $html;
        $this->php = $php;
        $this->text = $text;
        $this->xml = $xml;
    }
    /**
     * @psalm-assert-if-true !null $this->cacheDirectory
     */
    public function hasCacheDirectory() : bool
    {
        return $this->cacheDirectory !== null;
    }
    /**
     * @throws Exception
     */
    public function cacheDirectory() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Directory
    {
        if (!$this->hasCacheDirectory()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('No cache directory has been configured');
        }
        return $this->cacheDirectory;
    }
    public function hasNonEmptyListOfFilesToBeIncludedInCodeCoverageReport() : bool
    {
        return \count($this->directories) > 0 || \count($this->files) > 0;
    }
    public function directories() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Filter\DirectoryCollection
    {
        return $this->directories;
    }
    public function files() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection
    {
        return $this->files;
    }
    public function excludeDirectories() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Filter\DirectoryCollection
    {
        return $this->excludeDirectories;
    }
    public function excludeFiles() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection
    {
        return $this->excludeFiles;
    }
    public function pathCoverage() : bool
    {
        return $this->pathCoverage;
    }
    public function includeUncoveredFiles() : bool
    {
        return $this->includeUncoveredFiles;
    }
    public function ignoreDeprecatedCodeUnits() : bool
    {
        return $this->ignoreDeprecatedCodeUnits;
    }
    public function disableCodeCoverageIgnore() : bool
    {
        return $this->disableCodeCoverageIgnore;
    }
    public function processUncoveredFiles() : bool
    {
        return $this->processUncoveredFiles;
    }
    /**
     * @psalm-assert-if-true !null $this->clover
     */
    public function hasClover() : bool
    {
        return $this->clover !== null;
    }
    /**
     * @throws Exception
     */
    public function clover() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Clover
    {
        if (!$this->hasClover()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "Clover XML" has not been configured');
        }
        return $this->clover;
    }
    /**
     * @psalm-assert-if-true !null $this->cobertura
     */
    public function hasCobertura() : bool
    {
        return $this->cobertura !== null;
    }
    /**
     * @throws Exception
     */
    public function cobertura() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Cobertura
    {
        if (!$this->hasCobertura()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "Cobertura XML" has not been configured');
        }
        return $this->cobertura;
    }
    /**
     * @psalm-assert-if-true !null $this->crap4j
     */
    public function hasCrap4j() : bool
    {
        return $this->crap4j !== null;
    }
    /**
     * @throws Exception
     */
    public function crap4j() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Crap4j
    {
        if (!$this->hasCrap4j()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "Crap4J" has not been configured');
        }
        return $this->crap4j;
    }
    /**
     * @psalm-assert-if-true !null $this->html
     */
    public function hasHtml() : bool
    {
        return $this->html !== null;
    }
    /**
     * @throws Exception
     */
    public function html() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html
    {
        if (!$this->hasHtml()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "HTML" has not been configured');
        }
        return $this->html;
    }
    /**
     * @psalm-assert-if-true !null $this->php
     */
    public function hasPhp() : bool
    {
        return $this->php !== null;
    }
    /**
     * @throws Exception
     */
    public function php() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php
    {
        if (!$this->hasPhp()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "PHP" has not been configured');
        }
        return $this->php;
    }
    /**
     * @psalm-assert-if-true !null $this->text
     */
    public function hasText() : bool
    {
        return $this->text !== null;
    }
    /**
     * @throws Exception
     */
    public function text() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text
    {
        if (!$this->hasText()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "Text" has not been configured');
        }
        return $this->text;
    }
    /**
     * @psalm-assert-if-true !null $this->xml
     */
    public function hasXml() : bool
    {
        return $this->xml !== null;
    }
    /**
     * @throws Exception
     */
    public function xml() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Xml
    {
        if (!$this->hasXml()) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Exception('Code Coverage report "XML" has not been configured');
        }
        return $this->xml;
    }
}
