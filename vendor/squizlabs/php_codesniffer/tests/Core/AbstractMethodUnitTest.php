<?php

/**
 * Base class to use when testing utility methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core;

use Exception;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use ECSPrefix202408\PHPUnit\Framework\TestCase;
abstract class AbstractMethodUnitTest extends TestCase
{
    /**
     * The file extension of the test case file (without leading dot).
     *
     * This allows child classes to overrule the default `inc` with, for instance,
     * `js` or `css` when applicable.
     *
     * @var string
     */
    protected static $fileExtension = 'inc';
    /**
     * The tab width setting to use when tokenizing the file.
     *
     * This allows for test case files to use a different tab width than the default.
     *
     * @var integer
     */
    protected static $tabWidth = 4;
    /**
     * The \PHP_CodeSniffer\Files\File object containing the parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    protected static $phpcsFile;
    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * The test case file for a unit test class has to be in the same directory
     * directory and use the same file name as the test class, using the .inc extension.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeFile()
    {
        $_SERVER['argv'] = [];
        $config = new ConfigDouble();
        // Also set a tab-width to enable testing tab-replaced vs `orig_content`.
        $config->tabWidth = static::$tabWidth;
        $ruleset = new Ruleset($config);
        // Default to a file with the same name as the test class. Extension is property based.
        $relativeCN = \str_replace(__NAMESPACE__, '', \get_called_class());
        $relativePath = \str_replace('\\', \DIRECTORY_SEPARATOR, $relativeCN);
        $pathToTestFile = \realpath(__DIR__) . $relativePath . '.' . static::$fileExtension;
        // Make sure the file gets parsed correctly based on the file type.
        $contents = 'phpcs_input_file: ' . $pathToTestFile . \PHP_EOL;
        $contents .= \file_get_contents($pathToTestFile);
        self::$phpcsFile = new DummyFile($contents, $ruleset, $config);
        self::$phpcsFile->parse();
    }
    //end initializeFile()
    /**
     * Get the token pointer for a target token based on a specific comment found on the line before.
     *
     * Note: the test delimiter comment MUST start with "/* test" to allow this function to
     * distinguish between comments used *in* a test and test delimiters.
     *
     * @param string           $commentString The delimiter comment to look for.
     * @param int|string|array $tokenType     The type of token(s) to look for.
     * @param string           $tokenContent  Optional. The token content for the target token.
     *
     * @return int
     */
    public function getTargetToken($commentString, $tokenType, $tokenContent = null)
    {
        return self::getTargetTokenFromFile(self::$phpcsFile, $commentString, $tokenType, $tokenContent);
    }
    //end getTargetToken()
    /**
     * Get the token pointer for a target token based on a specific comment found on the line before.
     *
     * Note: the test delimiter comment MUST start with "/* test" to allow this function to
     * distinguish between comments used *in* a test and test delimiters.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile     The file to find the token in.
     * @param string                      $commentString The delimiter comment to look for.
     * @param int|string|array            $tokenType     The type of token(s) to look for.
     * @param string                      $tokenContent  Optional. The token content for the target token.
     *
     * @return int
     *
     * @throws Exception When the test delimiter comment is not found.
     * @throws Exception When the test target token is not found.
     */
    public static function getTargetTokenFromFile(File $phpcsFile, $commentString, $tokenType, $tokenContent = null)
    {
        $start = $phpcsFile->numTokens - 1;
        $comment = $phpcsFile->findPrevious(\T_COMMENT, $start, null, \false, $commentString);
        if ($comment === \false) {
            throw new Exception(\sprintf('Failed to find the test marker: %s in test case file %s', $commentString, $phpcsFile->getFilename()));
        }
        $tokens = $phpcsFile->getTokens();
        $end = $start + 1;
        // Limit the token finding to between this and the next delimiter comment.
        for ($i = $comment + 1; $i < $end; $i++) {
            if ($tokens[$i]['code'] !== \T_COMMENT) {
                continue;
            }
            if (\stripos($tokens[$i]['content'], '/* test') === 0) {
                $end = $i;
                break;
            }
        }
        $target = $phpcsFile->findNext($tokenType, $comment + 1, $end, \false, $tokenContent);
        if ($target === \false) {
            $msg = 'Failed to find test target token for comment string: ' . $commentString;
            if ($tokenContent !== null) {
                $msg .= ' with token content: ' . $tokenContent;
            }
            throw new Exception($msg);
        }
        return $target;
    }
    //end getTargetTokenFromFile()
    /**
     * Helper method to tell PHPUnit to expect a PHPCS RuntimeException in a PHPUnit cross-version
     * compatible manner.
     *
     * @param string $message The expected exception message.
     *
     * @return void
     */
    public function expectRunTimeException($message)
    {
        $exception = 'PHP_CodeSniffer\\Exceptions\\RuntimeException';
        if (\method_exists($this, 'expectException') === \true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $message);
        }
    }
    //end expectRunTimeException()
}
//end class
