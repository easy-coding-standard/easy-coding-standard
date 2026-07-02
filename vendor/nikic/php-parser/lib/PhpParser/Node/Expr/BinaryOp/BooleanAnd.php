<?php

declare (strict_types=1);
namespace ECSPrefix202607\PhpParser\Node\Expr\BinaryOp;

use ECSPrefix202607\PhpParser\Node\Expr\BinaryOp;
class BooleanAnd extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return '&&';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_BooleanAnd';
    }
}
