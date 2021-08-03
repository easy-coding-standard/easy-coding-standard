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
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml;

use DOMDocument;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Project extends \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml\Node
{
    public function __construct(string $directory)
    {
        $this->init();
        $this->setProjectSourceDirectory($directory);
    }
    public function projectSourceDirectory() : string
    {
        return $this->contextNode()->getAttribute('source');
    }
    public function buildInformation() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml\BuildInformation
    {
        $buildNode = $this->dom()->getElementsByTagNameNS('https://schema.phpunit.de/coverage/1.0', 'build')->item(0);
        if (!$buildNode) {
            $buildNode = $this->dom()->documentElement->appendChild($this->dom()->createElementNS('https://schema.phpunit.de/coverage/1.0', 'build'));
        }
        return new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml\BuildInformation($buildNode);
    }
    public function tests() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml\Tests
    {
        $testsNode = $this->contextNode()->getElementsByTagNameNS('https://schema.phpunit.de/coverage/1.0', 'tests')->item(0);
        if (!$testsNode) {
            $testsNode = $this->contextNode()->appendChild($this->dom()->createElementNS('https://schema.phpunit.de/coverage/1.0', 'tests'));
        }
        return new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml\Tests($testsNode);
    }
    public function asDom() : \DOMDocument
    {
        return $this->dom();
    }
    private function init() : void
    {
        $dom = new \DOMDocument();
        $dom->loadXML('<?xml version="1.0" ?><phpunit xmlns="https://schema.phpunit.de/coverage/1.0"><build/><project/></phpunit>');
        $this->setContextNode($dom->getElementsByTagNameNS('https://schema.phpunit.de/coverage/1.0', 'project')->item(0));
    }
    private function setProjectSourceDirectory(string $name) : void
    {
        $this->contextNode()->setAttribute('source', $name);
    }
}
