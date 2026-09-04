<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\ValueObject;

use ECSPrefix202609\PhpParser\Node\Expr;
final class ClassAndMethodArrayExprs
{
    /**
     * @readonly
     * @var \PhpParser\Node\Expr
     */
    private $classExpr;
    /**
     * @readonly
     * @var \PhpParser\Node\Expr
     */
    private $methodExpr;
    public function __construct(Expr $classExpr, Expr $methodExpr)
    {
        $this->classExpr = $classExpr;
        $this->methodExpr = $methodExpr;
    }
    public function getClassExpr(): Expr
    {
        return $this->classExpr;
    }
    public function getMethodExpr(): Expr
    {
        return $this->methodExpr;
    }
}
