<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr\Cast;

use ECSPrefix20210804\PhpParser\Node\Expr\Cast;
class Double extends \ECSPrefix20210804\PhpParser\Node\Expr\Cast
{
    // For use in "kind" attribute
    const KIND_DOUBLE = 1;
    // "double" syntax
    const KIND_FLOAT = 2;
    // "float" syntax
    const KIND_REAL = 3;
    // "real" syntax
    public function getType() : string
    {
        return 'Expr_Cast_Double';
    }
}
