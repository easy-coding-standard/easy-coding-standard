<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20220220\Nette\Neon;

/** @internal */
final class Traverser
{
    /** @var callable(Node): ?Node */
    private $callback;
    /** @param  callable(Node): ?Node  $callback */
    public function traverse(\ECSPrefix20220220\Nette\Neon\Node $node, callable $callback) : \ECSPrefix20220220\Nette\Neon\Node
    {
        $this->callback = $callback;
        return $this->traverseNode($node);
    }
    private function traverseNode(\ECSPrefix20220220\Nette\Neon\Node $node) : \ECSPrefix20220220\Nette\Neon\Node
    {
        $node = ($this->callback)($node) ?? $node;
        foreach ($node->getSubNodes() as &$subnode) {
            $subnode = $this->traverseNode($subnode);
        }
        return $node;
    }
}
