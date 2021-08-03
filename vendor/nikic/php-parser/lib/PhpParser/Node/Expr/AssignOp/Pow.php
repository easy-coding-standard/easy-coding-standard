<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Expr\AssignOp;

use ECSPrefix20210803\PhpParser\Node\Expr\AssignOp;
class Pow extends \ECSPrefix20210803\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}
