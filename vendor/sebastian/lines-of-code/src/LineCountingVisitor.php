<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/lines-of-code.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\LinesOfCode;

use function array_merge;
use function array_unique;
use function count;
use ECSPrefix20210803\PhpParser\Comment;
use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\Node\Expr;
use ECSPrefix20210803\PhpParser\NodeVisitorAbstract;
final class LineCountingVisitor extends \ECSPrefix20210803\PhpParser\NodeVisitorAbstract
{
    /**
     * @var int
     */
    private $linesOfCode;
    /**
     * @var Comment[]
     */
    private $comments = [];
    /**
     * @var int[]
     */
    private $linesWithStatements = [];
    public function __construct(int $linesOfCode)
    {
        $this->linesOfCode = $linesOfCode;
    }
    /**
     * @param \PhpParser\Node $node
     * @return void
     */
    public function enterNode($node)
    {
        $this->comments = \array_merge($this->comments, $node->getComments());
        if (!$node instanceof \ECSPrefix20210803\PhpParser\Node\Expr) {
            return;
        }
        $this->linesWithStatements[] = $node->getStartLine();
    }
    public function result() : \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode
    {
        $commentLinesOfCode = 0;
        foreach ($this->comments() as $comment) {
            $commentLinesOfCode += $comment->getEndLine() - $comment->getStartLine() + 1;
        }
        return new \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode($this->linesOfCode, $commentLinesOfCode, $this->linesOfCode - $commentLinesOfCode, \count(\array_unique($this->linesWithStatements)));
    }
    /**
     * @return Comment[]
     */
    private function comments() : array
    {
        $comments = [];
        foreach ($this->comments as $comment) {
            $comments[$comment->getStartLine() . '_' . $comment->getStartTokenPos() . '_' . $comment->getEndLine() . '_' . $comment->getEndTokenPos()] = $comment;
        }
        return $comments;
    }
}
