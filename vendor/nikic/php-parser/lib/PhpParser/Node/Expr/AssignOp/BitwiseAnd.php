<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\Node\Expr\AssignOp;

use ECSPrefix202608\PhpParser\Node\Expr\AssignOp;
class BitwiseAnd extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_BitwiseAnd';
    }
}
