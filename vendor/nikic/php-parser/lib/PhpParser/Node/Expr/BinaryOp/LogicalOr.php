<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\Node\Expr\BinaryOp;

use ECSPrefix202608\PhpParser\Node\Expr\BinaryOp;
class LogicalOr extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return 'or';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_LogicalOr';
    }
}
