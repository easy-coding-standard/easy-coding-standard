<?php

/**
 * Tests for the \PHP_CodeSniffer\Util\Common::stripColors() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Util\Common;

use PHP_CodeSniffer\Util\Common;
use ECSPrefix202408\PHPUnit\Framework\TestCase;
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::stripColors() method.
 *
 * @covers \PHP_CodeSniffer\Util\Common::stripColors
 */
final class StripColorsTest extends TestCase
{
    /**
     * Test stripping color codes from a text.
     *
     * @param string $text     The text provided.
     * @param string $expected Expected function output.
     *
     * @dataProvider dataStripColors
     *
     * @return void
     */
    public function testStripColors($text, $expected)
    {
        $this->assertSame($expected, Common::stripColors($text));
    }
    //end testStripColors()
    /**
     * Data provider.
     *
     * @see testStripColors()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataStripColors()
    {
        return ['Text is empty' => ['text' => '', 'expected' => ''], 'Text enclosed in color code' => ['text' => "\x1b[36mSome text\x1b[0m", 'expected' => 'Some text'], 'Text containing color code' => ['text' => "Some text \x1b[33mSome other text", 'expected' => 'Some text Some other text'], 'Text enclosed in color code, bold' => ['text' => "\x1b[1;32mSome text\x1b[0m", 'expected' => 'Some text'], 'Text enclosed in color code, with escaped text' => ['text' => "\x1b[30;1m\\n\x1b[0m", 'expected' => '\\n'], 'Text enclosed in color code, bold, dark, italic' => ['text' => "\x1b[1;2;3mtext\x1b[0m", 'expected' => 'text'], 'Text enclosed in color code, foreground color' => ['text' => "\x1b[38;5;255mtext\x1b[0m", 'expected' => 'text'], 'Text enclosed in color code, foreground color and background color' => ['text' => "\x1b[38;5;200;48;5;255mtext\x1b[0m", 'expected' => 'text'], 'Multiline text containing multiple color codes' => ['text' => "First \x1b[36mSecond\x1b[0m\nThird \x1b[1;2;3mFourth\nNext line\x1b[0m Last", 'expected' => 'First Second
Third Fourth
Next line Last']];
    }
    //end dataStripColors()
}
//end class
