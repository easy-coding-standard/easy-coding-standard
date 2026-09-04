<?php

declare (strict_types=1);
namespace ECSPrefix202609\PhpParser\Node\Expr\AssignOp;

use ECSPrefix202609\PhpParser\Node\Expr\AssignOp;
class Plus extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_Plus';
    }
}
