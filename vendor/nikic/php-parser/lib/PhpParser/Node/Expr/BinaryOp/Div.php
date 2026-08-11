<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\Node\Expr\BinaryOp;

use ECSPrefix202608\PhpParser\Node\Expr\BinaryOp;
class Div extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return '/';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_Div';
    }
}
