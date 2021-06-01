<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp;

use ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp;
class ShiftRight extends \ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '>>';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_ShiftRight';
    }
}
