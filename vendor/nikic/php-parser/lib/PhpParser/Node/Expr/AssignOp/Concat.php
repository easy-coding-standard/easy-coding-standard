<?php

declare (strict_types=1);
namespace ECSPrefix202607\PhpParser\Node\Expr\AssignOp;

use ECSPrefix202607\PhpParser\Node\Expr\AssignOp;
class Concat extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_Concat';
    }
}
