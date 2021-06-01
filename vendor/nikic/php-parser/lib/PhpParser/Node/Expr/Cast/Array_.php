<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Expr\Cast;

use ConfigTransformer20210601\PhpParser\Node\Expr\Cast;
class Array_ extends \ConfigTransformer20210601\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Array';
    }
}
