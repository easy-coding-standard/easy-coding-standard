<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\NodeVisitor;

use ECSPrefix202608\PhpParser\Node;
use ECSPrefix202608\PhpParser\NodeVisitorAbstract;
use function array_pop;
use function count;
/**
 * Visitor that connects a child node to its parent node.
 *
 * With <code>$weakReferences=false</code> on the child node, the parent node can be accessed through
 * <code>$node->getAttribute('parent')</code>.
 *
 * With <code>$weakReferences=true</code> the attribute name is "weak_parent" instead.
 */
final class ParentConnectingVisitor extends NodeVisitorAbstract
{
    /**
     * @var Node[]
     */
    private $stack = [];
    /**
     * @var bool
     */
    private $weakReferences;
    public function __construct(bool $weakReferences = \false)
    {
        $this->weakReferences = $weakReferences;
    }
    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }
    public function enterNode(Node $node)
    {
        if (!empty($this->stack)) {
            $parent = $this->stack[count($this->stack) - 1];
            if ($this->weakReferences) {
                $node->setAttribute('weak_parent', \WeakReference::create($parent));
            } else {
                $node->setAttribute('parent', $parent);
            }
        }
        $this->stack[] = $node;
    }
    public function leaveNode(Node $node)
    {
        array_pop($this->stack);
    }
}
