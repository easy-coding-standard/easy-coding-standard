<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser;

/**
 * @codeCoverageIgnore
 */
class NodeVisitorAbstract implements \ECSPrefix20210804\PhpParser\NodeVisitor
{
    public function beforeTraverse(array $nodes)
    {
        return null;
    }
    public function enterNode(\ECSPrefix20210804\PhpParser\Node $node)
    {
        return null;
    }
    public function leaveNode(\ECSPrefix20210804\PhpParser\Node $node)
    {
        return null;
    }
    public function afterTraverse(array $nodes)
    {
        return null;
    }
}
