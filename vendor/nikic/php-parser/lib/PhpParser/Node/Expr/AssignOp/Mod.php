<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Expr\AssignOp;

use ConfigTransformer20210601\PhpParser\Node\Expr\AssignOp;
class Mod extends \ConfigTransformer20210601\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Mod';
    }
}
