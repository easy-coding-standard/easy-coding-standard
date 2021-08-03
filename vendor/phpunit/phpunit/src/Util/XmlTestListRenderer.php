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
namespace ECSPrefix20210803\PHPUnit\Util;

use function get_class;
use function implode;
use function str_replace;
use ECSPrefix20210803\PHPUnit\Framework\TestCase;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite;
use ECSPrefix20210803\PHPUnit\Runner\PhptTestCase;
use RecursiveIteratorIterator;
use XMLWriter;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class XmlTestListRenderer
{
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function render(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : string
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(\true);
        $writer->startDocument();
        $writer->startElement('tests');
        $currentTestCase = null;
        foreach (new \RecursiveIteratorIterator($suite->getIterator()) as $test) {
            if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
                if (\get_class($test) !== $currentTestCase) {
                    if ($currentTestCase !== null) {
                        $writer->endElement();
                    }
                    $writer->startElement('testCaseClass');
                    $writer->writeAttribute('name', \get_class($test));
                    $currentTestCase = \get_class($test);
                }
                $writer->startElement('testCaseMethod');
                $writer->writeAttribute('name', $test->getName(\false));
                $writer->writeAttribute('groups', \implode(',', $test->getGroups()));
                if (!empty($test->getDataSetAsString(\false))) {
                    $writer->writeAttribute('dataSet', \str_replace(' with data set ', '', $test->getDataSetAsString(\false)));
                }
                $writer->endElement();
            } elseif ($test instanceof \ECSPrefix20210803\PHPUnit\Runner\PhptTestCase) {
                if ($currentTestCase !== null) {
                    $writer->endElement();
                    $currentTestCase = null;
                }
                $writer->startElement('phptFile');
                $writer->writeAttribute('path', $test->getName());
                $writer->endElement();
            }
        }
        if ($currentTestCase !== null) {
            $writer->endElement();
        }
        $writer->endElement();
        return $writer->outputMemory();
    }
}
