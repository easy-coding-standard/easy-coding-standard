<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp;

use ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp;
class Spaceship extends \ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '<=>';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Spaceship';
    }
}
