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
namespace ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration;

use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Logging\Logging;
use ECSPrefix20210803\PHPUnit\Util\Xml\ValidationResult;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Configuration
{
    /**
     * @var string
     */
    private $filename;
    /**
     * @var ValidationResult
     */
    private $validationResult;
    /**
     * @var ExtensionCollection
     */
    private $extensions;
    /**
     * @var CodeCoverage
     */
    private $codeCoverage;
    /**
     * @var Groups
     */
    private $groups;
    /**
     * @var Groups
     */
    private $testdoxGroups;
    /**
     * @var ExtensionCollection
     */
    private $listeners;
    /**
     * @var Logging
     */
    private $logging;
    /**
     * @var Php
     */
    private $php;
    /**
     * @var PHPUnit
     */
    private $phpunit;
    /**
     * @var TestSuiteCollection
     */
    private $testSuite;
    public function __construct(string $filename, \ECSPrefix20210803\PHPUnit\Util\Xml\ValidationResult $validationResult, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\ExtensionCollection $extensions, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage $codeCoverage, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Groups $groups, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Groups $testdoxGroups, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\ExtensionCollection $listeners, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Logging\Logging $logging, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Php $php, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\PHPUnit $phpunit, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\TestSuiteCollection $testSuite)
    {
        $this->filename = $filename;
        $this->validationResult = $validationResult;
        $this->extensions = $extensions;
        $this->codeCoverage = $codeCoverage;
        $this->groups = $groups;
        $this->testdoxGroups = $testdoxGroups;
        $this->listeners = $listeners;
        $this->logging = $logging;
        $this->php = $php;
        $this->phpunit = $phpunit;
        $this->testSuite = $testSuite;
    }
    public function filename() : string
    {
        return $this->filename;
    }
    public function hasValidationErrors() : bool
    {
        return $this->validationResult->hasValidationErrors();
    }
    public function validationErrors() : string
    {
        return $this->validationResult->asString();
    }
    public function extensions() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\ExtensionCollection
    {
        return $this->extensions;
    }
    public function codeCoverage() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage
    {
        return $this->codeCoverage;
    }
    public function groups() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Groups
    {
        return $this->groups;
    }
    public function testdoxGroups() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Groups
    {
        return $this->testdoxGroups;
    }
    public function listeners() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\ExtensionCollection
    {
        return $this->listeners;
    }
    public function logging() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Logging\Logging
    {
        return $this->logging;
    }
    public function php() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Php
    {
        return $this->php;
    }
    public function phpunit() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\PHPUnit
    {
        return $this->phpunit;
    }
    public function testSuite() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\TestSuiteCollection
    {
        return $this->testSuite;
    }
}
