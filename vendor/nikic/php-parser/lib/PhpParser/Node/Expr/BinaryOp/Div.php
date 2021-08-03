<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp;

use ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp;
class Div extends \ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '/';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Div';
    }
}
