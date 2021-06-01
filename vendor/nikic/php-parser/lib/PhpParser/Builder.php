<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser;

interface Builder
{
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : \ConfigTransformer20210601\PhpParser\Node;
}
