<?php

namespace Symplify\PackageBuilder\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;

final class StaticInputDetector
{
    /**
     * @return bool
     */
    public static function isDebug()
    {
        $argvInput = new ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
