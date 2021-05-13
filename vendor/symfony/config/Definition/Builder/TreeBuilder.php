<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210513\Symfony\Component\Config\Definition\Builder;

use ECSPrefix20210513\Symfony\Component\Config\Definition\NodeInterface;
/**
 * This is the entry class for building a config tree.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TreeBuilder implements \ECSPrefix20210513\Symfony\Component\Config\Definition\Builder\NodeParentInterface
{
    protected $tree;
    protected $root;
    /**
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type = 'array', \ECSPrefix20210513\Symfony\Component\Config\Definition\Builder\NodeBuilder $builder = null)
    {
        $name = (string) $name;
        $type = (string) $type;
        $builder = isset($builder) ? $builder : new \ECSPrefix20210513\Symfony\Component\Config\Definition\Builder\NodeBuilder();
        $this->root = $builder->node($name, $type)->setParent($this);
    }
    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition The root node (as an ArrayNodeDefinition when the type is 'array')
     */
    public function getRootNode()
    {
        return $this->root;
    }
    /**
     * Builds the tree.
     *
     * @return NodeInterface
     *
     * @throws \RuntimeException
     */
    public function buildTree()
    {
        if (null !== $this->tree) {
            return $this->tree;
        }
        return $this->tree = $this->root->getNode(\true);
    }
    /**
     * @param string $separator
     */
    public function setPathSeparator($separator)
    {
        $separator = (string) $separator;
        // unset last built as changing path separator changes all nodes
        $this->tree = null;
        $this->root->setPathSeparator($separator);
    }
}
