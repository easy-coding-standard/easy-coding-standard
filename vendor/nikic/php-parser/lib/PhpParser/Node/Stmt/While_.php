<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Stmt;

use ConfigTransformer20210601\PhpParser\Node;
class While_ extends \ConfigTransformer20210601\PhpParser\Node\Stmt
{
    /** @var Node\Expr Condition */
    public $cond;
    /** @var Node\Stmt[] Statements */
    public $stmts;
    /**
     * Constructs a while node.
     *
     * @param Node\Expr   $cond       Condition
     * @param Node\Stmt[] $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(\ConfigTransformer20210601\PhpParser\Node\Expr $cond, array $stmts = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->stmts = $stmts;
    }
    public function getSubNodeNames() : array
    {
        return ['cond', 'stmts'];
    }
    public function getType() : string
    {
        return 'Stmt_While';
    }
}
