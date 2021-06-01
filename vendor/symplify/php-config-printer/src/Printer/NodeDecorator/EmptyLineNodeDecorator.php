<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Printer\NodeDecorator;

use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\Node\Expr\Assign;
use ConfigTransformer20210601\PhpParser\Node\Expr\Closure;
use ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
use ConfigTransformer20210601\PhpParser\Node\Stmt;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Expression;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Nop;
use ConfigTransformer20210601\PhpParser\NodeFinder;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class EmptyLineNodeDecorator
{
    /**
     * @var NodeFinder
     */
    private $nodeFinder;
    public function __construct(\ConfigTransformer20210601\PhpParser\NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }
    /**
     * @param Node[] $stmts
     * @return void
     */
    public function decorate(array $stmts)
    {
        $closure = $this->nodeFinder->findFirstInstanceOf($stmts, \ConfigTransformer20210601\PhpParser\Node\Expr\Closure::class);
        if (!$closure instanceof \ConfigTransformer20210601\PhpParser\Node\Expr\Closure) {
            throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        $newStmts = [];
        foreach ($closure->stmts as $key => $closureStmt) {
            if ($this->shouldAddEmptyLineBeforeStatement($key, $closureStmt)) {
                $newStmts[] = new \ConfigTransformer20210601\PhpParser\Node\Stmt\Nop();
            }
            $newStmts[] = $closureStmt;
        }
        $closure->stmts = $newStmts;
    }
    private function shouldAddEmptyLineBeforeStatement(int $key, \ConfigTransformer20210601\PhpParser\Node\Stmt $stmt) : bool
    {
        // do not add space before first item
        if ($key === 0) {
            return \false;
        }
        if (!$stmt instanceof \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression) {
            return \false;
        }
        $expr = $stmt->expr;
        if ($expr instanceof \ConfigTransformer20210601\PhpParser\Node\Expr\Assign) {
            return \true;
        }
        return $expr instanceof \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
    }
}
