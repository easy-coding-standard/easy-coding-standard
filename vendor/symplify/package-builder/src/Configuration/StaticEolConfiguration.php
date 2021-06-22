<?php

declare (strict_types=1);
namespace ECSPrefix20210622\Symplify\PackageBuilder\Configuration;

final class StaticEolConfiguration
{
    public static function getEolChar() : string
    {
        return "\n";
    }
}
