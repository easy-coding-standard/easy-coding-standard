<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr\Cast;

use ECSPrefix20210804\PhpParser\Node\Expr\Cast;
class Unset_ extends \ECSPrefix20210804\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}
