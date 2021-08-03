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
namespace ECSPrefix20210803\SebastianBergmann\Complexity;

use function assert;
use function is_array;
use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\Node\Name;
use ECSPrefix20210803\PhpParser\Node\Stmt;
use ECSPrefix20210803\PhpParser\Node\Stmt\Class_;
use ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod;
use ECSPrefix20210803\PhpParser\Node\Stmt\Function_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Trait_;
use ECSPrefix20210803\PhpParser\NodeTraverser;
use ECSPrefix20210803\PhpParser\NodeVisitorAbstract;
final class ComplexityCalculatingVisitor extends \ECSPrefix20210803\PhpParser\NodeVisitorAbstract
{
    /**
     * @psalm-var list<Complexity>
     */
    private $result = [];
    /**
     * @var bool
     */
    private $shortCircuitTraversal;
    public function __construct(bool $shortCircuitTraversal)
    {
        $this->shortCircuitTraversal = $shortCircuitTraversal;
    }
    /**
     * @param \PhpParser\Node $node
     * @return int|null
     */
    public function enterNode($node)
    {
        if (!$node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod && !$node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Function_) {
            return null;
        }
        if ($node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod) {
            $name = $this->classMethodName($node);
        } else {
            $name = $this->functionName($node);
        }
        $statements = $node->getStmts();
        \assert(\is_array($statements));
        $this->result[] = new \ECSPrefix20210803\SebastianBergmann\Complexity\Complexity($name, $this->cyclomaticComplexity($statements));
        if ($this->shortCircuitTraversal) {
            return \ECSPrefix20210803\PhpParser\NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        return null;
    }
    public function result() : \ECSPrefix20210803\SebastianBergmann\Complexity\ComplexityCollection
    {
        return \ECSPrefix20210803\SebastianBergmann\Complexity\ComplexityCollection::fromList(...$this->result);
    }
    /**
     * @param Stmt[] $statements
     */
    private function cyclomaticComplexity(array $statements) : int
    {
        $traverser = new \ECSPrefix20210803\PhpParser\NodeTraverser();
        $cyclomaticComplexityCalculatingVisitor = new \ECSPrefix20210803\SebastianBergmann\Complexity\CyclomaticComplexityCalculatingVisitor();
        $traverser->addVisitor($cyclomaticComplexityCalculatingVisitor);
        /* @noinspection UnusedFunctionResultInspection */
        $traverser->traverse($statements);
        return $cyclomaticComplexityCalculatingVisitor->cyclomaticComplexity();
    }
    private function classMethodName(\ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod $node) : string
    {
        $parent = $node->getAttribute('parent');
        \assert($parent instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Class_ || $parent instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Trait_);
        \assert(isset($parent->namespacedName));
        \assert($parent->namespacedName instanceof \ECSPrefix20210803\PhpParser\Node\Name);
        return $parent->namespacedName->toString() . '::' . $node->name->toString();
    }
    private function functionName(\ECSPrefix20210803\PhpParser\Node\Stmt\Function_ $node) : string
    {
        \assert(isset($node->namespacedName));
        \assert($node->namespacedName instanceof \ECSPrefix20210803\PhpParser\Node\Name);
        return $node->namespacedName->toString();
    }
}
