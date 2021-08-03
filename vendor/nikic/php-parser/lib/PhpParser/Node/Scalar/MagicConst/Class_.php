<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst;

use ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst;
class Class_ extends \ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__CLASS__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Class';
    }
}
