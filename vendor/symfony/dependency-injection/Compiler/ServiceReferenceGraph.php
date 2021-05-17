<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210517\Symfony\Component\DependencyInjection\Compiler;

use ECSPrefix20210517\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
/**
 * This is a directed graph of your services.
 *
 * This information can be used by your compiler passes instead of collecting
 * it themselves which improves performance quite a lot.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @final
 */
class ServiceReferenceGraph
{
    /**
     * @var ServiceReferenceGraphNode[]
     */
    private $nodes = [];
    /**
     * @param string $id
     * @return bool
     */
    public function hasNode($id)
    {
        $id = (string) $id;
        return isset($this->nodes[$id]);
    }
    /**
     * Gets a node by identifier.
     *
     * @throws InvalidArgumentException if no node matches the supplied identifier
     * @param string $id
     * @return \Symfony\Component\DependencyInjection\Compiler\ServiceReferenceGraphNode
     */
    public function getNode($id)
    {
        $id = (string) $id;
        if (!isset($this->nodes[$id])) {
            throw new \ECSPrefix20210517\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('There is no node with id "%s".', $id));
        }
        return $this->nodes[$id];
    }
    /**
     * Returns all nodes.
     *
     * @return mixed[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }
    /**
     * Clears all nodes.
     */
    public function clear()
    {
        foreach ($this->nodes as $node) {
            $node->clear();
        }
        $this->nodes = [];
    }
    /**
     * Connects 2 nodes together in the Graph.
     * @param string|null $sourceId
     * @param string|null $destId
     * @param bool $lazy
     * @param bool $weak
     * @param bool $byConstructor
     */
    public function connect($sourceId, $sourceValue, $destId, $destValue = null, $reference = null, $lazy = \false, $weak = \false, $byConstructor = \false)
    {
        $lazy = (bool) $lazy;
        $weak = (bool) $weak;
        $byConstructor = (bool) $byConstructor;
        if (null === $sourceId || null === $destId) {
            return;
        }
        $sourceNode = $this->createNode($sourceId, $sourceValue);
        $destNode = $this->createNode($destId, $destValue);
        $edge = new \ECSPrefix20210517\Symfony\Component\DependencyInjection\Compiler\ServiceReferenceGraphEdge($sourceNode, $destNode, $reference, $lazy, $weak, $byConstructor);
        $sourceNode->addOutEdge($edge);
        $destNode->addInEdge($edge);
    }
    /**
     * @param string $id
     * @return \Symfony\Component\DependencyInjection\Compiler\ServiceReferenceGraphNode
     */
    private function createNode($id, $value)
    {
        $id = (string) $id;
        if (isset($this->nodes[$id]) && $this->nodes[$id]->getValue() === $value) {
            return $this->nodes[$id];
        }
        return $this->nodes[$id] = new \ECSPrefix20210517\Symfony\Component\DependencyInjection\Compiler\ServiceReferenceGraphNode($id, $value);
    }
}
