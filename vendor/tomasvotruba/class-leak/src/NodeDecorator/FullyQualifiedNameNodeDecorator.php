<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\ClassLeak\NodeDecorator;

use ECSPrefix202609\PhpParser\Node\Stmt;
use ECSPrefix202609\PhpParser\NodeTraverser;
use ECSPrefix202609\PhpParser\NodeVisitor\NameResolver;
use ECSPrefix202609\PhpParser\NodeVisitor\NodeConnectingVisitor;
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
