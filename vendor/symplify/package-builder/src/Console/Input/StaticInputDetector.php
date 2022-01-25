<?php

declare (strict_types=1);
namespace ECSPrefix20220125\Symplify\PackageBuilder\Console\Input;

use ECSPrefix20220125\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new \ECSPrefix20220125\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
