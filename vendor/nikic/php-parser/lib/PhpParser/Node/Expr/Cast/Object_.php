<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Expr\Cast;

use ECSPrefix20210803\PhpParser\Node\Expr\Cast;
class Object_ extends \ECSPrefix20210803\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}
