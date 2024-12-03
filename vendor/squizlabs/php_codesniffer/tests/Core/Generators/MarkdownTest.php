<?php

/**
 * Tests the Markdown documentation generation.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Generators;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Generators\Fixtures\MarkdownDouble;
use ECSPrefix202412\PHPUnit\Framework\TestCase;
/**
 * Test the Markdown documentation generation.
 *
 * @covers \PHP_CodeSniffer\Generators\Markdown
 * @group  Windows
 */
final class MarkdownTest extends TestCase
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
        $generator = new MarkdownDouble($ruleset);
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
        return ['Standard without docs' => ['standard' => __DIR__ . '/NoDocsTest.xml', 'pathToExpected' => __DIR__ . '/Expectations/ExpectedOutputNoDocs.md'], 'Standard with one doc file' => ['standard' => __DIR__ . '/OneDocTest.xml', 'pathToExpected' => __DIR__ . '/Expectations/ExpectedOutputOneDoc.md'], 'Standard with multiple doc files' => ['standard' => __DIR__ . '/StructureDocsTest.xml', 'pathToExpected' => __DIR__ . '/Expectations/ExpectedOutputStructureDocs.md']];
    }
    //end dataDocs()
    /**
     * Test the generated footer.
     *
     * @return void
     */
    public function testFooter()
    {
        // Set up the ruleset.
        $standard = __DIR__ . '/OneDocTest.xml';
        $config = new ConfigDouble(["--standard={$standard}"]);
        $ruleset = new Ruleset($config);
        $regex = '`^Documentation generated on [A-Z][a-z]{2}, [0-9]{2} [A-Z][a-z]{2} 20[0-9]{2} [0-2][0-9](?::[0-5][0-9]){2} [+-][0-9]{4}';
        $regex .= ' by \\[PHP_CodeSniffer [3-9]\\.[0-9]+.[0-9]+\\]\\(https://github\\.com/PHPCSStandards/PHP_CodeSniffer\\)\\R$`';
        $this->expectOutputRegex($regex);
        $generator = new MarkdownDouble($ruleset);
        $generator->printRealFooter();
    }
    //end testFooter()
}
//end class
