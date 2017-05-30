<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Legacy;

use Nette\Loaders\RobotLoader;
use PHP_CodeSniffer\Util\Tokens;

final class LegacyCompatibilityLayer
{
    /**
     * @var bool
     */
    private static $isAdded = false;

    public static function add(): void
    {
        if (self::$isAdded) {
            return;
        }

        self::autoloadCodeSniffer();
        self::setupVerbosityToMakeLegacyCodeRun();
        new Tokens;

        self::$isAdded = true;
    }

    private static function setupVerbosityToMakeLegacyCodeRun(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
    }

    private static function autoloadCodeSniffer(): void
    {
        $robotLoader = new RobotLoader;
        $robotLoader->acceptFiles = '*.php';
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/_robot_loader');
        $robotLoader->addDirectory(getcwd() . '/vendor/squizlabs/php_codesniffer/src');
        $robotLoader->register();
    }
}
