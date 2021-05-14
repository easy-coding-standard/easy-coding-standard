<?php

namespace Symplify\PackageBuilder\Console\Input;

use ECSPrefix20210514\Symfony\Component\Console\Input\ArgvInput;
final class StaticInputDetector
{
    /**
     * @return bool
     */
    public static function isDebug()
    {
        $argvInput = new \ECSPrefix20210514\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
