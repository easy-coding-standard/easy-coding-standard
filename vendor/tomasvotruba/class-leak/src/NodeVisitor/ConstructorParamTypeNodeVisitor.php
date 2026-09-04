<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\ClassLeak\NodeVisitor;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\ComplexType;
use ECSPrefix202609\PhpParser\Node\IntersectionType;
use ECSPrefix202609\PhpParser\Node\Name;
use ECSPrefix202609\PhpParser\Node\NullableType;
use ECSPrefix202609\PhpParser\Node\Stmt;
use ECSPrefix202609\PhpParser\Node\Stmt\ClassMethod;
use ECSPrefix202609\PhpParser\Node\UnionType;
use ECSPrefix202609\PhpParser\NodeVisitorAbstract;
final class ConstructorParamTypeNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $paramTypeNames = [];
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->paramTypeNames = [];
        return $nodes;
    }
    /**
     * @return null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }
        if ($node->name->toLowerString() !== '__construct') {
            return null;
        }
        foreach ($node->params as $param) {
            if ($param->type === null) {
                continue;
            }
            foreach ($this->resolveTypeNames($param->type) as $typeName) {
                $this->paramTypeNames[] = $typeName;
            }
        }
        return null;
    }
    /**
     * @return string[]
     */
    public function getParamTypeNames(): array
    {
        return array_unique($this->paramTypeNames);
    }
    /**
     * @param Node\Identifier|Name|ComplexType $type
     * @return string[]
     */
    private function resolveTypeNames(Node $type): array
    {
        if ($type instanceof Name) {
            return [$type->toString()];
        }
        if ($type instanceof NullableType) {
            return $this->resolveTypeNames($type->type);
        }
        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            $typeNames = [];
            foreach ($type->types as $innerType) {
                $typeNames = array_merge($typeNames, $this->resolveTypeNames($innerType));
            }
            return $typeNames;
        }
        // builtin Identifier type, e.g. string, int
        return [];
    }
}
