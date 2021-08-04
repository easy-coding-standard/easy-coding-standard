<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Doubler\Generator\Node;

/**
 * Argument node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ArgumentNode
{
    private $name;
    private $default;
    private $optional = \false;
    private $byReference = \false;
    private $isVariadic = \false;
    /** @var ArgumentTypeNode */
    private $typeNode;
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->typeNode = new \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode();
    }
    public function getName()
    {
        return $this->name;
    }
    public function setTypeNode(\ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode $typeNode)
    {
        $this->typeNode = $typeNode;
    }
    public function getTypeNode() : \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode
    {
        return $this->typeNode;
    }
    public function hasDefault()
    {
        return $this->isOptional() && !$this->isVariadic();
    }
    public function getDefault()
    {
        return $this->default;
    }
    public function setDefault($default = null)
    {
        $this->optional = \true;
        $this->default = $default;
    }
    public function isOptional()
    {
        return $this->optional;
    }
    public function setAsPassedByReference($byReference = \true)
    {
        $this->byReference = $byReference;
    }
    public function isPassedByReference()
    {
        return $this->byReference;
    }
    public function setAsVariadic($isVariadic = \true)
    {
        $this->isVariadic = $isVariadic;
    }
    public function isVariadic()
    {
        return $this->isVariadic;
    }
    /**
     * @deprecated use getArgumentTypeNode instead
     * @return string|null
     */
    public function getTypeHint()
    {
        $type = $this->typeNode->getNonNullTypes() ? $this->typeNode->getNonNullTypes()[0] : null;
        return $type ? \ltrim($type, '\\') : null;
    }
    /**
     * @deprecated use setArgumentTypeNode instead
     * @param string|null $typeHint
     */
    public function setTypeHint($typeHint = null)
    {
        $this->typeNode = $typeHint === null ? new \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode() : new \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode($typeHint);
    }
    /**
     * @deprecated use getArgumentTypeNode instead
     * @return bool
     */
    public function isNullable()
    {
        return $this->typeNode->canUseNullShorthand();
    }
    /**
     * @deprecated use getArgumentTypeNode instead
     * @param bool $isNullable
     */
    public function setAsNullable($isNullable = \true)
    {
        $nonNullTypes = $this->typeNode->getNonNullTypes();
        $this->typeNode = $isNullable ? new \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode('null', ...$nonNullTypes) : new \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\ArgumentTypeNode(...$nonNullTypes);
    }
}
