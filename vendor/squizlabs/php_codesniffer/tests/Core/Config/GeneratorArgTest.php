<?php

/**
 * Tests for the \PHP_CodeSniffer\Config --generator argument.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Config;

use PHP_CodeSniffer\Tests\ConfigDouble;
use ECSPrefix202501\PHPUnit\Framework\TestCase;
/**
 * Tests for the \PHP_CodeSniffer\Config --generator argument.
 *
 * @covers \PHP_CodeSniffer\Config::processLongArgument
 */
final class GeneratorArgTest extends TestCase
{
    /**
     * Ensure that the generator property is set when the parameter is passed a valid value.
     *
     * @param string $generatorName Generator name.
     *
     * @dataProvider dataGeneratorNames
     *
     * @return void
     */
    public function testGenerators($generatorName)
    {
        $config = new ConfigDouble(["--generator={$generatorName}"]);
        $this->assertSame($generatorName, $config->generator);
    }
    //end testGenerators()
    /**
     * Data provider for testGenerators().
     *
     * @see self::testGenerators()
     *
     * @return array<int, array<string>>
     */
    public static function dataGeneratorNames()
    {
        return [['Text'], ['HTML'], ['Markdown']];
    }
    //end dataGeneratorNames()
    /**
     * Ensure that only the first argument is processed and others are ignored.
     *
     * @return void
     */
    public function testOnlySetOnce()
    {
        $config = new ConfigDouble(['--generator=Text', '--generator=HTML', '--generator=InvalidGenerator']);
        $this->assertSame('Text', $config->generator);
    }
    //end testOnlySetOnce()
}
//end class
