<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\NodeVisitor;

use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\NodeVisitorAbstract;
/**
 * Visitor cloning all nodes and linking to the original nodes using an attribute.
 *
 * This visitor is required to perform format-preserving pretty prints.
 */
class CloningVisitor extends \ConfigTransformer20210601\PhpParser\NodeVisitorAbstract
{
    public function enterNode(\ConfigTransformer20210601\PhpParser\Node $origNode)
    {
        $node = clone $origNode;
        $node->setAttribute('origNode', $origNode);
        return $node;
    }
}
