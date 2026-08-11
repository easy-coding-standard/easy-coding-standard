<?php

declare (strict_types=1);
namespace ECSPrefix202608\TomasVotruba\ClassLeak\NodeDecorator;

use ECSPrefix202608\PhpParser\Node\Stmt;
use ECSPrefix202608\PhpParser\NodeTraverser;
use ECSPrefix202608\PhpParser\NodeVisitor\NameResolver;
use ECSPrefix202608\PhpParser\NodeVisitor\NodeConnectingVisitor;
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
