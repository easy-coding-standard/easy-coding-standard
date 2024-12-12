<?php

/**
 * Tests for the \PHP_CodeSniffer\Util\Tokens::tokenName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Util\Tokens;

use PHP_CodeSniffer\Util\Tokens;
use ECSPrefix202412\PHPUnit\Framework\TestCase;
/**
 * Tests for the \PHP_CodeSniffer\Util\Tokens::tokenName() method.
 *
 * @covers \PHP_CodeSniffer\Util\Tokens::tokenName
 */
final class TokenNameTest extends TestCase
{
    /**
     * Test the method.
     *
     * @param int|string $tokenCode The PHP/PHPCS token code to get the name for.
     * @param string     $expected  The expected token name.
     *
     * @dataProvider dataTokenName
     *
     * @return void
     */
    public function testTokenName($tokenCode, $expected)
    {
        $this->assertSame($expected, Tokens::tokenName($tokenCode));
    }
    //end testTokenName()
    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataTokenName()
    {
        return [
            'PHP native token: T_ECHO' => ['tokenCode' => \T_ECHO, 'expected' => 'T_ECHO'],
            'PHP native token: T_FUNCTION' => ['tokenCode' => \T_FUNCTION, 'expected' => 'T_FUNCTION'],
            'PHPCS native token: T_CLOSURE' => ['tokenCode' => \T_CLOSURE, 'expected' => 'T_CLOSURE'],
            'PHPCS native token: T_STRING_CONCAT' => ['tokenCode' => \T_STRING_CONCAT, 'expected' => 'T_STRING_CONCAT'],
            // Document the current behaviour for invalid input.
            // This behaviour is subject to change.
            'Non-token integer passed' => ['tokenCode' => 100000, 'expected' => 'UNKNOWN'],
            'Non-token string passed' => ['tokenCode' => 'something', 'expected' => 'ing'],
        ];
    }
    //end dataTokenName()
}
//end class
