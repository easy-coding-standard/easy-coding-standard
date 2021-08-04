<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp;

use ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp;
class LogicalXor extends \ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return 'xor';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_LogicalXor';
    }
}
