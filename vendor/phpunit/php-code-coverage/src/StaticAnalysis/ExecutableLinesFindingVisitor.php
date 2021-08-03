<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis;

use function array_unique;
use function sort;
use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\Node\Stmt\Break_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Case_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Catch_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Continue_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Do_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Echo_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Else_;
use ECSPrefix20210803\PhpParser\Node\Stmt\ElseIf_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Expression;
use ECSPrefix20210803\PhpParser\Node\Stmt\Finally_;
use ECSPrefix20210803\PhpParser\Node\Stmt\For_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Foreach_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Goto_;
use ECSPrefix20210803\PhpParser\Node\Stmt\If_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Return_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Switch_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Throw_;
use ECSPrefix20210803\PhpParser\Node\Stmt\TryCatch;
use ECSPrefix20210803\PhpParser\Node\Stmt\Unset_;
use ECSPrefix20210803\PhpParser\Node\Stmt\While_;
use ECSPrefix20210803\PhpParser\NodeVisitorAbstract;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ExecutableLinesFindingVisitor extends \ECSPrefix20210803\PhpParser\NodeVisitorAbstract
{
    /**
     * @psalm-var list<int>
     */
    private $executableLines = [];
    public function enterNode(\ECSPrefix20210803\PhpParser\Node $node) : void
    {
        if (!$this->isExecutable($node)) {
            return;
        }
        $this->executableLines[] = $node->getStartLine();
    }
    /**
     * @psalm-return list<int>
     */
    public function executableLines() : array
    {
        $executableLines = \array_unique($this->executableLines);
        \sort($executableLines);
        return $executableLines;
    }
    private function isExecutable(\ECSPrefix20210803\PhpParser\Node $node) : bool
    {
        return $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Break_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Case_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Catch_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Continue_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Do_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Echo_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ElseIf_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Else_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Expression || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Finally_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Foreach_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\For_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Goto_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\If_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Return_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Switch_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Throw_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\TryCatch || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Unset_ || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\While_;
    }
}
