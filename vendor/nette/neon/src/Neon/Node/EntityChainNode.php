<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20220220\Nette\Neon\Node;

use ECSPrefix20220220\Nette\Neon;
use ECSPrefix20220220\Nette\Neon\Node;
/** @internal */
final class EntityChainNode extends \ECSPrefix20220220\Nette\Neon\Node
{
    /** @var EntityNode[] */
    public $chain = [];
    public function __construct(array $chain = [], int $startPos = null, int $endPos = null)
    {
        $this->chain = $chain;
        $this->startPos = $startPos;
        $this->endPos = $endPos ?? $startPos;
    }
    public function toValue() : \ECSPrefix20220220\Nette\Neon\Entity
    {
        $entities = [];
        foreach ($this->chain as $item) {
            $entities[] = $item->toValue();
        }
        return new \ECSPrefix20220220\Nette\Neon\Entity(\ECSPrefix20220220\Nette\Neon\Neon::CHAIN, $entities);
    }
    public function toString() : string
    {
        return \implode('', \array_map(function ($entity) {
            return $entity->toString();
        }, $this->chain));
    }
    public function getSubNodes() : array
    {
        $res = [];
        foreach ($this->chain as &$item) {
            $res[] =& $item;
        }
        return $res;
    }
}
