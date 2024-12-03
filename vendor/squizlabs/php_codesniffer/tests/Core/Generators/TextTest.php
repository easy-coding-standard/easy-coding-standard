<?php

/**
 * Tests the Text documentation generation.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Generators;

use PHP_CodeSniffer\Generators\Text;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use ECSPrefix202412\PHPUnit\Framework\TestCase;
/**
 * Test the Text documentation generation.
 *
 * @covers \PHP_CodeSniffer\Generators\Text
 * @group  Windows
 */
final class TextTest extends TestCase
{
    /**
     * Test the generated docs.
     *
     * @param string $standard       The standard to use for the test.
     * @param string $pathToExpected Path to a file containing the expected function output.
     *
     * @dataProvider dataDocs
     *
     * @return void
     */
    public function testDocs($standard, $pathToExpected)
    {
        // Set up the ruleset.
        $config = new ConfigDouble(["--standard={$standard}"]);
        $ruleset = new Ruleset($config);
        $expected = \file_get_contents($pathToExpected);
        $this->assertNotFalse($expected, 'Output expectation file could not be found');
        // Make the test OS independent.
        $expected = \str_replace("\n", \PHP_EOL, $expected);
        $this->expectOutputString($expected);
        $generator = new Text($ruleset);
        $generator->generate();
    }
    //end testDocs()
    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataDocs()
    {
        return ['Standard without docs' => ['standard' => __DIR__ . '/NoDocsTest.xml', 'pathToExpected' => __DIR__ . '/Expectations/ExpectedOutputEmpty.txt'], 'Standard with one doc file' => ['standard' => __DIR__ . '/OneDocTest.xml', 'pathToExpected' => __DIR__ . '/Expectations/ExpectedOutputOneDoc.txt'], 'Standard with multiple doc files' => ['standard' => __DIR__ . '/StructureDocsTest.xml', 'pathToExpected' => __DIR__ . '/Expectations/ExpectedOutputStructureDocs.txt']];
    }
    //end dataDocs()
}
//end class
