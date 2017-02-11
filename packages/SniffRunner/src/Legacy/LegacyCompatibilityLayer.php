<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Legacy;

use PHP_CodeSniffer\Util\Tokens;

final class LegacyCompatibilityLayer
{
    /**
     * @var bool
     */
    private static $isAdded = false;

    public static function add() : void
    {
        if (self::$isAdded) {
            return;
        }

        self::ensureLineEndingsAreDetected();
        self::setupVerbosityToMakeLegacyCodeRun();
        new Tokens();

        self::$isAdded = true;
    }

    /**
     * Ensure this option is enabled or else line endings will not always
     * be detected properly for files created on a Mac with the /r line ending.
     */
    private static function ensureLineEndingsAreDetected() : void
    {
        ini_set('auto_detect_line_endings', 'true');
    }

    private static function setupVerbosityToMakeLegacyCodeRun() : void
    {
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
    }
}
