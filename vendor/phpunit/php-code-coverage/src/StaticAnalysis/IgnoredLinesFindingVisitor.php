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
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis;

use function array_merge;
use function range;
use function strpos;
use ECSPrefix20210804\PhpParser\Node;
use ECSPrefix20210804\PhpParser\Node\Stmt\Class_;
use ECSPrefix20210804\PhpParser\Node\Stmt\ClassMethod;
use ECSPrefix20210804\PhpParser\Node\Stmt\Function_;
use ECSPrefix20210804\PhpParser\Node\Stmt\Interface_;
use ECSPrefix20210804\PhpParser\Node\Stmt\Trait_;
use ECSPrefix20210804\PhpParser\NodeTraverser;
use ECSPrefix20210804\PhpParser\NodeVisitorAbstract;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class IgnoredLinesFindingVisitor extends \ECSPrefix20210804\PhpParser\NodeVisitorAbstract
{
    /**
     * @psalm-var list<int>
     */
    private $ignoredLines = [];
    /**
     * @var bool
     */
    private $useAnnotationsForIgnoringCode;
    /**
     * @var bool
     */
    private $ignoreDeprecated;
    public function __construct(bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecated)
    {
        $this->useAnnotationsForIgnoringCode = $useAnnotationsForIgnoringCode;
        $this->ignoreDeprecated = $ignoreDeprecated;
    }
    public function enterNode(\ECSPrefix20210804\PhpParser\Node $node) : ?int
    {
        if (!$node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Class_ && !$node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Trait_ && !$node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Interface_ && !$node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\ClassMethod && !$node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Function_) {
            return null;
        }
        if ($node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Class_ && $node->isAnonymous()) {
            return null;
        }
        // Workaround for https://bugs.xdebug.org/view.php?id=1798
        if ($node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Class_ || $node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Trait_ || $node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Interface_) {
            $this->ignoredLines[] = $node->getStartLine();
        }
        if (!$this->useAnnotationsForIgnoringCode) {
            return null;
        }
        if ($node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Interface_) {
            return null;
        }
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return null;
        }
        if (\strpos($docComment->getText(), '@codeCoverageIgnore') !== \false) {
            $this->ignoredLines = \array_merge($this->ignoredLines, \range($node->getStartLine(), $node->getEndLine()));
        }
        if ($this->ignoreDeprecated && \strpos($docComment->getText(), '@deprecated') !== \false) {
            $this->ignoredLines = \array_merge($this->ignoredLines, \range($node->getStartLine(), $node->getEndLine()));
        }
        if ($node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\ClassMethod || $node instanceof \ECSPrefix20210804\PhpParser\Node\Stmt\Function_) {
            return \ECSPrefix20210804\PhpParser\NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        return null;
    }
    /**
     * @psalm-return list<int>
     */
    public function ignoredLines() : array
    {
        return $this->ignoredLines;
    }
}
