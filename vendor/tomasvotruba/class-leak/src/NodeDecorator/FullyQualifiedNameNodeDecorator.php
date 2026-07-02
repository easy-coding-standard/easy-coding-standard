<?php

declare (strict_types=1);
namespace ECSPrefix202607\TomasVotruba\ClassLeak\NodeDecorator;

use ECSPrefix202607\PhpParser\Node\Stmt;
use ECSPrefix202607\PhpParser\NodeTraverser;
use ECSPrefix202607\PhpParser\NodeVisitor\NameResolver;
use ECSPrefix202607\PhpParser\NodeVisitor\NodeConnectingVisitor;
final class FullyQualifiedNameNodeDecorator
{
    /**
     * @param Stmt[] $stmts
     */
    public function decorate(array $stmts): void
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor(new NodeConnectingVisitor());
        $nodeTraverser->traverse($stmts);
    }
}
