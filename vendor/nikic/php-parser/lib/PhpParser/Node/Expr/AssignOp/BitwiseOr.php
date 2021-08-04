<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr\AssignOp;

use ECSPrefix20210804\PhpParser\Node\Expr\AssignOp;
class BitwiseOr extends \ECSPrefix20210804\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseOr';
    }
}
