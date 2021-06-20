<?php

declare (strict_types=1);
namespace ECSPrefix20210620\Symplify\PackageBuilder\Configuration;

final class StaticEolConfiguration
{
    public static function getEolChar() : string
    {
        return "\n";
    }
}
