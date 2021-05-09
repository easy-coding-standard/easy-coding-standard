<?php

namespace Symplify\EasyTesting\PHPUnit;

final class StaticPHPUnitEnvironment
{
    /**
     * Never ever used static methods if not neccesary, this is just handy for tests + src to prevent duplication.
     * @return bool
     */
    public static function isPHPUnitRun()
    {
        return \defined('PHPUNIT_COMPOSER_INSTALL') || \defined('__PHPUNIT_PHAR__');
    }
}
