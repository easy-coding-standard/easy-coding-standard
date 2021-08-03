<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser;

interface Builder
{
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : \ECSPrefix20210803\PhpParser\Node;
}
