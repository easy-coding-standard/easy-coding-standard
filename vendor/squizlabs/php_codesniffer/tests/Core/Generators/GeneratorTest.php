<?php

/**
 * Tests the functionality in the abstract Generator class.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Generators;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Generators\Fixtures\MockGenerator;
use ECSPrefix202412\PHPUnit\Framework\TestCase;
/**
 * Tests the functionality in the abstract Generator class.
 *
 * @covers \PHP_CodeSniffer\Generators\Generator
 * @group  Windows
 */
final class GeneratorTest extends TestCase
{
    /**
     * Test the list of available documentation for a standard is generated correctly.
     *
     * @param string        $standard The standard to use for the test.
     * @param array<string> $expected The expected list of found docs.
     *
     * @dataProvider dataConstructor
     *
     * @return void
     */
    public function testConstructor($standard, array $expected)
    {
        // Set up the ruleset.
        $config = new ConfigDouble(["--standard={$standard}"]);
        $ruleset = new Ruleset($config);
        $generator = new MockGenerator($ruleset);
        $this->assertSame($expected, $generator->docFiles);
    }
    //end testConstructor()
    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataConstructor()
    {
        $pathToDocsInFixture = __DIR__ . \DIRECTORY_SEPARATOR . 'Fixtures';
        $pathToDocsInFixture .= \DIRECTORY_SEPARATOR . 'StandardWithDocs';
        $pathToDocsInFixture .= \DIRECTORY_SEPARATOR . 'Docs' . \DIRECTORY_SEPARATOR;
        return ['Standard without docs' => ['standard' => __DIR__ . '/NoDocsTest.xml', 'expected' => []], 'Standard with an invalid doc file' => ['standard' => __DIR__ . '/NoValidDocsTest.xml', 'expected' => [$pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'NoDocumentationElementStandard.xml']], 'Standard with one doc file' => ['standard' => __DIR__ . '/OneDocTest.xml', 'expected' => [$pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'OneStandardBlockNoCodeStandard.xml']], 'Standard with multiple doc files' => ['standard' => __DIR__ . '/StructureDocsTest.xml', 'expected' => [$pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'NoContentStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'OneCodeComparisonNoStandardStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'OneStandardBlockCodeComparisonStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'OneStandardBlockNoCodeStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'OneStandardBlockTwoCodeComparisonsStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'TwoStandardBlocksNoCodeStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'TwoStandardBlocksOneCodeComparisonStandard.xml', $pathToDocsInFixture . 'Structure' . \DIRECTORY_SEPARATOR . 'TwoStandardBlocksThreeCodeComparisonsStandard.xml']]];
    }
    //end dataConstructor()
    /**
     * Verify that an XML doc which isn't valid documentation yields an Exception to warn devs.
     *
     * This should not be hidden via defensive coding!
     *
     * @return void
     */
    public function testGeneratingInvalidDocsResultsInException()
    {
        // Set up the ruleset.
        $standard = __DIR__ . '/NoValidDocsTest.xml';
        $config = new ConfigDouble(["--standard={$standard}"]);
        $ruleset = new Ruleset($config);
        if (\PHP_VERSION_ID >= 80000) {
            $exception = 'TypeError';
            $message = 'processSniff(): Argument #1 ($doc) must be of type DOMNode, null given';
        } else {
            if (\PHP_VERSION_ID >= 70000) {
                $exception = 'TypeError';
                $message = 'processSniff() must be an instance of DOMNode, null given';
            } else {
                $exception = 'PHPUnit_Framework_Error';
                $message = 'processSniff() must be an instance of DOMNode, null given';
            }
        }
        if (\method_exists($this, 'expectExceptionMessage') === \true) {
            // PHPUnit 5.2.0+.
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
        } else {
            // Ancient PHPUnit.
            $this->setExpectedException($exception, $message);
        }
        $generator = new MockGenerator($ruleset);
        $generator->generate();
    }
    //end testGeneratingInvalidDocsResultsInException()
    /**
     * Verify the wiring for the generate() function.
     *
     * @param string $standard The standard to use for the test.
     * @param string $expected The expected function output.
     *
     * @dataProvider dataGeneratingDocs
     *
     * @return void
     */
    public function testGeneratingDocs($standard, $expected)
    {
        // Set up the ruleset.
        $config = new ConfigDouble(["--standard={$standard}"]);
        $ruleset = new Ruleset($config);
        $this->expectOutputString($expected);
        $generator = new MockGenerator($ruleset);
        $generator->generate();
    }
    //end testGeneratingDocs()
    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGeneratingDocs()
    {
        $multidocExpected = [];
        $multidocExpected[] = 'No Content';
        $multidocExpected[] = 'Code Comparison Only, Missing Standard Block';
        $multidocExpected[] = 'One Standard Block, Code Comparison';
        $multidocExpected[] = 'One Standard Block, No Code';
        $multidocExpected[] = 'One Standard Block, Two Code Comparisons';
        $multidocExpected[] = 'Two Standard Blocks, No Code';
        $multidocExpected[] = 'Two Standard Blocks, One Code Comparison';
        $multidocExpected[] = 'Two Standard Blocks, Three Code Comparisons';
        $multidocExpected = \implode(\PHP_EOL, $multidocExpected) . \PHP_EOL;
        return ['Standard without docs' => ['standard' => __DIR__ . '/NoDocsTest.xml', 'expected' => ''], 'Standard with one doc file' => ['standard' => __DIR__ . '/OneDocTest.xml', 'expected' => 'One Standard Block, No Code' . \PHP_EOL], 'Standard with multiple doc files' => ['standard' => __DIR__ . '/StructureDocsTest.xml', 'expected' => $multidocExpected]];
    }
    //end dataGeneratingDocs()
    /**
     * Test that the documentation for each standard passed on the command-line is shown separately.
     *
     * @covers \PHP_CodeSniffer\Runner::runPHPCS
     *
     * @return void
     */
    public function testGeneratorWillShowEachStandardSeparately()
    {
        $standard = __DIR__ . '/OneDocTest.xml';
        $_SERVER['argv'] = ['phpcs', '--generator=Text', "--standard={$standard},PSR1", '--report-width=80'];
        $regex = '`^
            \\R*                                                      # Optional blank line at the start.
            (?:
                (?P<delimiter>-+\\R)                                  # Line with dashes.
                \\|[ ]GENERATORTEST[ ]CODING[ ]STANDARD:[ ][^\\|]+\\|\\R # Doc title line with prefix expected for first standard.
                (?P>delimiter)                                       # Line with dashes.
                .+?\\R{2}                                             # Standard description.
            )                                                        # Only expect this group once.
            (?:
                (?P>delimiter)                                       # Line with dashes.
                \\|[ ]PSR1[ ]CODING[ ]STANDARD:[ ][^\\|]+\\|\\R          # Doc title line with prefix expected for second standard.
                (?P>delimiter)                                       # Line with dashes.
                .+?\\R+                                               # Standard description.
                (?:
                    -+[ ]CODE[ ]COMPARISON[ ]-+\\R                    # Code Comparison starter line with dashes.
                    (?:.+?(?P>delimiter)\\R){2}                       # Arbitrary text followed by a delimiter line.
                )*                                                   # Code comparison is optional and can exist multiple times.
                \\R+
            ){3,}                                                    # This complete group should occur at least three times.
            `sx';
        $this->expectOutputRegex($regex);
        $runner = new Runner();
        $runner->runPHPCS();
    }
    //end testGeneratorWillShowEachStandardSeparately()
}
//end class
