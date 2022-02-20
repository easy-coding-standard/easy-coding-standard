<?php

/**
 * A test class for testing the core.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Tests\FileList;
use ECSPrefix20220220\PHPUnit\TextUI\TestRunner;
use ECSPrefix20220220\PHPUnit\Framework\TestSuite;
class AllTests
{
    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        \ECSPrefix20220220\PHPUnit\TextUI\TestRunner::run(self::suite());
    }
    //end main()
    /**
     * Add all core unit tests into a test suite.
     *
     * @return \PHPUnit\Framework\TestSuite
     */
    public static function suite()
    {
        $suite = new \ECSPrefix20220220\PHPUnit\Framework\TestSuite('PHP CodeSniffer Core');
        $testFileIterator = new \PHP_CodeSniffer\Tests\FileList(__DIR__, '', '`Test\\.php$`Di');
        foreach ($testFileIterator->fileIterator as $file) {
            if (\strpos($file, 'AbstractMethodUnitTest.php') !== \false) {
                continue;
            }
            include_once $file;
            $class = \str_replace(__DIR__, '', $file);
            $class = \str_replace('.php', '', $class);
            $class = \str_replace('/', '\\', $class);
            $class = 'PHP_CodeSniffer\\Tests\\Core' . $class;
            $suite->addTestSuite($class);
        }
        return $suite;
    }
    //end suite()
}
//end class
