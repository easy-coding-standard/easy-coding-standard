<?php

declare (strict_types=1);
namespace ECSPrefix20210805\Symplify\PackageBuilder\Console\Input;

use ECSPrefix20210805\Symfony\Component\Console\Input\ArgvInput;
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new \ECSPrefix20210805\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
