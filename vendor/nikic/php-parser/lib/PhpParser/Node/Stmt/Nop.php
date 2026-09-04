<?php

declare (strict_types=1);
namespace ECSPrefix202609\PhpParser\Node\Stmt;

use ECSPrefix202609\PhpParser\Node;
/** Nop/empty statement (;). */
class Nop extends Node\Stmt
{
    public function getSubNodeNames(): array
    {
        return [];
    }
    public function getType(): string
    {
        return 'Stmt_Nop';
    }
}
