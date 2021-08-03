<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Stmt;

use ECSPrefix20210803\PhpParser\Node;
/** Nop/empty statement (;). */
class Nop extends \ECSPrefix20210803\PhpParser\Node\Stmt
{
    public function getSubNodeNames() : array
    {
        return [];
    }
    public function getType() : string
    {
        return 'Stmt_Nop';
    }
}
