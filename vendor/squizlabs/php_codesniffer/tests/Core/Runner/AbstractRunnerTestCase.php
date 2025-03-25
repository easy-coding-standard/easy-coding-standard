<?php

/**
 * Base class to use for tests invoking the Runner class.
 *
 * As those tests will use the _real_ Config class instead of the ConfigDouble, we need to ensure
 * this doesn't negatively impact other tests, what with the Config using static properties.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Runner;

use ECSPrefix202503\PHPUnit\Framework\TestCase;
use ReflectionProperty;
abstract class AbstractRunnerTestCase extends TestCase
{
    /**
     * Set static properties in the Config class to prevent tests influencing each other.
     *
     * @before
     *
     * @return void
     */
    public function setConfigStatics()
    {
        // Set to the property's default value to clear out potentially set values from other tests.
        self::setStaticConfigProperty('overriddenDefaults', []);
        self::setStaticConfigProperty('executablePaths', []);
        // Set to values which prevent the test-runner user's `CodeSniffer.conf` file
        // from being read and influencing the tests.
        self::setStaticConfigProperty('configData', []);
        self::setStaticConfigProperty('configDataFile', '');
    }
    //end setConfigStatics()
    /**
     * Clean up after each finished test.
     *
     * @after
     *
     * @return void
     */
    public function clearArgv()
    {
        $_SERVER['argv'] = [];
    }
    //end clearArgv()
    /**
     * Reset the static properties in the Config class to their true defaults to prevent this class
     * from influencing other tests.
     *
     * @afterClass
     *
     * @return void
     */
    public static function reset()
    {
        self::setStaticConfigProperty('overriddenDefaults', []);
        self::setStaticConfigProperty('executablePaths', []);
        self::setStaticConfigProperty('configData', null);
        self::setStaticConfigProperty('configDataFile', null);
        $_SERVER['argv'] = [];
    }
    //end reset()
    /**
     * Helper function to set a static property on the Config class.
     *
     * @param string $name  The name of the property to set.
     * @param mixed  $value The value to set the property to.
     *
     * @return void
     */
    public static function setStaticConfigProperty($name, $value)
    {
        $property = new ReflectionProperty('PHP_CodeSniffer\\Config', $name);
        $property->setAccessible(\true);
        $property->setValue(null, $value);
        $property->setAccessible(\false);
    }
    //end setStaticConfigProperty()
}
//end class
