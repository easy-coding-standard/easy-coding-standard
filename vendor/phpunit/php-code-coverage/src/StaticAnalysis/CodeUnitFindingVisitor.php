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

use function implode;
use function rtrim;
use function trim;
use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\Node\Identifier;
use ECSPrefix20210803\PhpParser\Node\Name;
use ECSPrefix20210803\PhpParser\Node\NullableType;
use ECSPrefix20210803\PhpParser\Node\Stmt\Class_;
use ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod;
use ECSPrefix20210803\PhpParser\Node\Stmt\Function_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Interface_;
use ECSPrefix20210803\PhpParser\Node\Stmt\Trait_;
use ECSPrefix20210803\PhpParser\Node\UnionType;
use ECSPrefix20210803\PhpParser\NodeTraverser;
use ECSPrefix20210803\PhpParser\NodeVisitorAbstract;
use ECSPrefix20210803\SebastianBergmann\Complexity\CyclomaticComplexityCalculatingVisitor;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class CodeUnitFindingVisitor extends \ECSPrefix20210803\PhpParser\NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $classes = [];
    /**
     * @var array
     */
    private $traits = [];
    /**
     * @var array
     */
    private $functions = [];
    public function enterNode(\ECSPrefix20210803\PhpParser\Node $node)
    {
        if ($node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Class_) {
            if ($node->isAnonymous()) {
                return;
            }
            $this->processClass($node);
        }
        if ($node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Trait_) {
            $this->processTrait($node);
        }
        if (!$node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod && !$node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Function_) {
            return null;
        }
        if ($node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod) {
            $parentNode = $node->getAttribute('parent');
            if ($parentNode instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Class_ && $parentNode->isAnonymous()) {
                return;
            }
            $this->processMethod($node);
            return;
        }
        $this->processFunction($node);
    }
    public function classes() : array
    {
        return $this->classes;
    }
    public function traits() : array
    {
        return $this->traits;
    }
    public function functions() : array
    {
        return $this->functions;
    }
    /**
     * @psalm-param ClassMethod|Function_ $node
     */
    private function cyclomaticComplexity(\ECSPrefix20210803\PhpParser\Node $node) : int
    {
        \assert($node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Function_);
        $nodes = $node->getStmts();
        if ($nodes === null) {
            return 0;
        }
        $traverser = new \ECSPrefix20210803\PhpParser\NodeTraverser();
        $cyclomaticComplexityCalculatingVisitor = new \ECSPrefix20210803\SebastianBergmann\Complexity\CyclomaticComplexityCalculatingVisitor();
        $traverser->addVisitor($cyclomaticComplexityCalculatingVisitor);
        /* @noinspection UnusedFunctionResultInspection */
        $traverser->traverse($nodes);
        return $cyclomaticComplexityCalculatingVisitor->cyclomaticComplexity();
    }
    /**
     * @psalm-param ClassMethod|Function_ $node
     */
    private function signature(\ECSPrefix20210803\PhpParser\Node $node) : string
    {
        \assert($node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod || $node instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Function_);
        $signature = ($node->returnsByRef() ? '&' : '') . $node->name->toString() . '(';
        $parameters = [];
        foreach ($node->getParams() as $parameter) {
            \assert(isset($parameter->var->name));
            $parameterAsString = '';
            if ($parameter->type !== null) {
                $parameterAsString = $this->type($parameter->type) . ' ';
            }
            $parameterAsString .= '$' . $parameter->var->name;
            /* @todo Handle default values */
            $parameters[] = $parameterAsString;
        }
        $signature .= \implode(', ', $parameters) . ')';
        $returnType = $node->getReturnType();
        if ($returnType !== null) {
            $signature .= ': ' . $this->type($returnType);
        }
        return $signature;
    }
    /**
     * @psalm-param Identifier|Name|NullableType|UnionType $type
     */
    private function type(\ECSPrefix20210803\PhpParser\Node $type) : string
    {
        \assert($type instanceof \ECSPrefix20210803\PhpParser\Node\Identifier || $type instanceof \ECSPrefix20210803\PhpParser\Node\Name || $type instanceof \ECSPrefix20210803\PhpParser\Node\NullableType || $type instanceof \ECSPrefix20210803\PhpParser\Node\UnionType);
        if ($type instanceof \ECSPrefix20210803\PhpParser\Node\NullableType) {
            return '?' . $type->type;
        }
        if ($type instanceof \ECSPrefix20210803\PhpParser\Node\UnionType) {
            $types = [];
            foreach ($type->types as $_type) {
                $types[] = $_type->toString();
            }
            return \implode('|', $types);
        }
        return $type->toString();
    }
    private function visibility(\ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod $node) : string
    {
        if ($node->isPrivate()) {
            return 'private';
        }
        if ($node->isProtected()) {
            return 'protected';
        }
        return 'public';
    }
    private function processClass(\ECSPrefix20210803\PhpParser\Node\Stmt\Class_ $node) : void
    {
        $name = $node->name->toString();
        $namespacedName = $node->namespacedName->toString();
        $this->classes[$namespacedName] = ['name' => $name, 'namespacedName' => $namespacedName, 'namespace' => $this->namespace($namespacedName, $name), 'startLine' => $node->getStartLine(), 'endLine' => $node->getEndLine(), 'methods' => []];
    }
    private function processTrait(\ECSPrefix20210803\PhpParser\Node\Stmt\Trait_ $node) : void
    {
        $name = $node->name->toString();
        $namespacedName = $node->namespacedName->toString();
        $this->traits[$namespacedName] = ['name' => $name, 'namespacedName' => $namespacedName, 'namespace' => $this->namespace($namespacedName, $name), 'startLine' => $node->getStartLine(), 'endLine' => $node->getEndLine(), 'methods' => []];
    }
    private function processMethod(\ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod $node) : void
    {
        $parentNode = $node->getAttribute('parent');
        if ($parentNode instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Interface_) {
            return;
        }
        \assert($parentNode instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Class_ || $parentNode instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Trait_);
        \assert(isset($parentNode->name));
        \assert(isset($parentNode->namespacedName));
        \assert($parentNode->namespacedName instanceof \ECSPrefix20210803\PhpParser\Node\Name);
        $parentName = $parentNode->name->toString();
        $parentNamespacedName = $parentNode->namespacedName->toString();
        if ($parentNode instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Class_) {
            $storage =& $this->classes;
        } else {
            $storage =& $this->traits;
        }
        if (!isset($storage[$parentNamespacedName])) {
            $storage[$parentNamespacedName] = ['name' => $parentName, 'namespacedName' => $parentNamespacedName, 'namespace' => $this->namespace($parentNamespacedName, $parentName), 'startLine' => $parentNode->getStartLine(), 'endLine' => $parentNode->getEndLine(), 'methods' => []];
        }
        $storage[$parentNamespacedName]['methods'][$node->name->toString()] = ['methodName' => $node->name->toString(), 'signature' => $this->signature($node), 'visibility' => $this->visibility($node), 'startLine' => $node->getStartLine(), 'endLine' => $node->getEndLine(), 'ccn' => $this->cyclomaticComplexity($node)];
    }
    private function processFunction(\ECSPrefix20210803\PhpParser\Node\Stmt\Function_ $node) : void
    {
        \assert(isset($node->name));
        \assert(isset($node->namespacedName));
        \assert($node->namespacedName instanceof \ECSPrefix20210803\PhpParser\Node\Name);
        $name = $node->name->toString();
        $namespacedName = $node->namespacedName->toString();
        $this->functions[$namespacedName] = ['name' => $name, 'namespacedName' => $namespacedName, 'namespace' => $this->namespace($namespacedName, $name), 'signature' => $this->signature($node), 'startLine' => $node->getStartLine(), 'endLine' => $node->getEndLine(), 'ccn' => $this->cyclomaticComplexity($node)];
    }
    private function namespace(string $namespacedName, string $name) : string
    {
        return \trim(\rtrim($namespacedName, $name), '\\');
    }
}
