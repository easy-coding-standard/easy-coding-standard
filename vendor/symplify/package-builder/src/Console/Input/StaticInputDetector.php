<?php

declare (strict_types=1);
namespace ECSPrefix20220130\Symplify\PackageBuilder\Console\Input;

use ECSPrefix20220130\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new \ECSPrefix20220130\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
