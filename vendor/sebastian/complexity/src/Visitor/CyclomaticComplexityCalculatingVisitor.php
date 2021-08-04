<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/complexity.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\Complexity;

use function get_class;
use ECSPrefix20210804\PhpParser\Node;
use ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\BooleanOr;
use ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\LogicalOr;
use ECSPrefix20210804\PhpParser\Node\Expr\Ternary;
use ECSPrefix20210804\PhpParser\Node\Stmt\Case_;
use ECSPrefix20210804\PhpParser\Node\Stmt\Catch_;
use ECSPrefix20210804\PhpParser\Node\Stmt\ElseIf_;
use ECSPrefix20210804\PhpParser\Node\Stmt\For_;
use ECSPrefix20210804\PhpParser\Node\Stmt\Foreach_;
use ECSPrefix20210804\PhpParser\Node\Stmt\If_;
use ECSPrefix20210804\PhpParser\Node\Stmt\While_;
use ECSPrefix20210804\PhpParser\NodeVisitorAbstract;
final class CyclomaticComplexityCalculatingVisitor extends \ECSPrefix20210804\PhpParser\NodeVisitorAbstract
{
    /**
     * @var int
     */
    private $cyclomaticComplexity = 1;
    /**
     * @param \PhpParser\Node $node
     * @return void
     */
    public function enterNode($node)
    {
        /* @noinspection GetClassMissUseInspection */
        switch (\get_class($node)) {
            case \ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\BooleanAnd::class:
            case \ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\BooleanOr::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\Case_::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\Catch_::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\ElseIf_::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\For_::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\Foreach_::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\If_::class:
            case \ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\LogicalAnd::class:
            case \ECSPrefix20210804\PhpParser\Node\Expr\BinaryOp\LogicalOr::class:
            case \ECSPrefix20210804\PhpParser\Node\Expr\Ternary::class:
            case \ECSPrefix20210804\PhpParser\Node\Stmt\While_::class:
                $this->cyclomaticComplexity++;
        }
    }
    public function cyclomaticComplexity() : int
    {
        return $this->cyclomaticComplexity;
    }
}
