<?php

declare (strict_types=1);
namespace ECSPrefix202607\PhpParser\Node\Expr\Cast;

use ECSPrefix202607\PhpParser\Node\Expr\Cast;
class Void_ extends Cast
{
    public function getType(): string
    {
        return 'Expr_Cast_Void';
    }
}
