<?php

/**
 * Tests for the \PHP_CodeSniffer\Config --sniffs and --exclude arguments.
 *
 * @author  Dan Wallis <dan@wallis.nz>
 * @license https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Config;

use PHP_CodeSniffer\Tests\ConfigDouble;
use ECSPrefix202408\PHPUnit\Framework\TestCase;
/**
 * Tests for the \PHP_CodeSniffer\Config --sniffs and --exclude arguments.
 *
 * @covers \PHP_CodeSniffer\Config::processLongArgument
 */
final class SniffsExcludeArgsTest extends TestCase
{
    /**
     * Ensure that the expected error message is returned for invalid arguments.
     *
     * @param string $argument 'sniffs' or 'exclude'.
     * @param string $value    List of sniffs to include / exclude.
     * @param string $message  Expected error message text.
     *
     * @return       void
     * @dataProvider dataInvalidSniffs
     */
    public function testInvalid($argument, $value, $message)
    {
        $exception = 'PHP_CodeSniffer\\Exceptions\\DeepExitException';
        if (\method_exists($this, 'expectException') === \true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $message);
        }
        new ConfigDouble(["--{$argument}={$value}"]);
    }
    //end testInvalid()
    /**
     * Data provider for testInvalid().
     *
     * @see    self::testInvalid()
     * @return array<string, array<string, string>>
     */
    public static function dataInvalidSniffs()
    {
        $arguments = ['sniffs', 'exclude'];
        $data = [];
        $messageTemplate = 'ERROR: The specified sniff code "%s" is invalid' . \PHP_EOL . \PHP_EOL;
        foreach ($arguments as $argument) {
            // An empty string is not a valid sniff.
            $data[$argument . '; empty string'] = ['argument' => $argument, 'value' => '', 'message' => \sprintf($messageTemplate, '')];
            // A standard is not a valid sniff.
            $data[$argument . '; standard'] = ['argument' => $argument, 'value' => 'Standard', 'message' => \sprintf($messageTemplate, 'Standard')];
            // A category is not a valid sniff.
            $data[$argument . '; category'] = ['argument' => $argument, 'value' => 'Standard.Category', 'message' => \sprintf($messageTemplate, 'Standard.Category')];
            // An error-code is not a valid sniff.
            $data[$argument . '; error-code'] = ['argument' => $argument, 'value' => 'Standard.Category', 'message' => \sprintf($messageTemplate, 'Standard.Category')];
            // Only the first error is reported.
            $data[$argument . '; two errors'] = ['argument' => $argument, 'value' => 'StandardOne,StandardTwo', 'message' => \sprintf($messageTemplate, 'StandardOne')];
            $data[$argument . '; valid followed by invalid'] = ['argument' => $argument, 'value' => 'StandardOne.Category.Sniff,StandardTwo.Category', 'message' => \sprintf($messageTemplate, 'StandardTwo.Category')];
        }
        //end foreach
        return $data;
    }
    //end dataInvalidSniffs()
    /**
     * Ensure that the valid data does not throw an exception, and the value is stored.
     *
     * @param string $argument 'sniffs' or 'exclude'.
     * @param string $value    List of sniffs to include or exclude.
     *
     * @return       void
     * @dataProvider dataValidSniffs
     */
    public function testValid($argument, $value)
    {
        $config = new ConfigDouble(["--{$argument}={$value}"]);
        $this->assertSame(\explode(',', $value), $config->{$argument});
    }
    //end testValid()
    /**
     * Data provider for testValid().
     *
     * @see    self::testValid()
     * @return array<string, array<string, string>>
     */
    public static function dataValidSniffs()
    {
        $arguments = ['sniffs', 'exclude'];
        $data = [];
        foreach ($arguments as $argument) {
            $data[$argument . '; one valid sniff'] = ['argument' => $argument, 'value' => 'Standard.Category.Sniff'];
            $data[$argument . '; two valid sniffs'] = ['argument' => $argument, 'value' => 'StandardOne.Category.Sniff,StandardTwo.Category.Sniff'];
        }
        return $data;
    }
    //end dataValidSniffs()
    /**
     * Ensure that only the first argument is processed and others are ignored.
     *
     * @param string $argument 'sniffs' or 'exclude'.
     *
     * @return       void
     * @dataProvider dataOnlySetOnce
     */
    public function testOnlySetOnce($argument)
    {
        $config = new ConfigDouble(["--{$argument}=StandardOne.Category.Sniff", "--{$argument}=StandardTwo.Category.Sniff", "--{$argument}=Standard.AnotherCategory.Sniff"]);
        $this->assertSame(['StandardOne.Category.Sniff'], $config->{$argument});
    }
    //end testOnlySetOnce()
    /**
     * Data provider for testOnlySetOnce().
     *
     * @return array<string, array<string>>
     */
    public static function dataOnlySetOnce()
    {
        return ['sniffs' => ['sniffs'], 'exclude' => ['exclude']];
    }
    //end dataOnlySetOnce()
}
//end class
