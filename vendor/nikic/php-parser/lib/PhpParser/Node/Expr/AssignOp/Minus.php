<?php

declare (strict_types=1);
namespace ECSPrefix202606\PhpParser\Node\Expr\AssignOp;

use ECSPrefix202606\PhpParser\Node\Expr\AssignOp;
class Minus extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_Minus';
    }
}
