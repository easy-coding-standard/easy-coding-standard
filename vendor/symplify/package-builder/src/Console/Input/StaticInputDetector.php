<?php

declare (strict_types=1);
namespace ECSPrefix20211119\Symplify\PackageBuilder\Console\Input;

use ECSPrefix20211119\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new \ECSPrefix20211119\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
