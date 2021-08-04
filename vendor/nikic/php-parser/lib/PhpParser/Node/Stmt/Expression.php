<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Stmt;

use ECSPrefix20210804\PhpParser\Node;
/**
 * Represents statements of type "expr;"
 */
class Expression extends \ECSPrefix20210804\PhpParser\Node\Stmt
{
    /** @var Node\Expr Expression */
    public $expr;
    /**
     * Constructs an expression statement.
     *
     * @param Node\Expr $expr       Expression
     * @param array     $attributes Additional attributes
     */
    public function __construct(\ECSPrefix20210804\PhpParser\Node\Expr $expr, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }
    public function getSubNodeNames() : array
    {
        return ['expr'];
    }
    public function getType() : string
    {
        return 'Stmt_Expression';
    }
}
