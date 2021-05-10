<?php

namespace Symplify\PackageBuilder\Console\Input;

use ECSPrefix20210510\Symfony\Component\Console\Input\ArgvInput;
final class StaticInputDetector
{
    /**
     * @return bool
     */
    public static function isDebug()
    {
        $argvInput = new \ECSPrefix20210510\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
