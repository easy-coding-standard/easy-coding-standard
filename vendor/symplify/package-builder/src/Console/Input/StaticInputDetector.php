<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\PackageBuilder\Console\Input;

use ECSPrefix202305\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
