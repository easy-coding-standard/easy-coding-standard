<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\NodeVisitor;

use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\NodeVisitorAbstract;
/**
 * Visitor cloning all nodes and linking to the original nodes using an attribute.
 *
 * This visitor is required to perform format-preserving pretty prints.
 */
class CloningVisitor extends \ECSPrefix20210803\PhpParser\NodeVisitorAbstract
{
    public function enterNode(\ECSPrefix20210803\PhpParser\Node $origNode)
    {
        $node = clone $origNode;
        $node->setAttribute('origNode', $origNode);
        return $node;
    }
}
