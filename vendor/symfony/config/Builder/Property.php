<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210713\Symfony\Component\Config\Builder;

/**
 * Represents a property when building classes.
 *
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Property
{
    private $name;
    private $originalName;
    private $array = \false;
    private $type = null;
    private $content;
    public function __construct(string $originalName, string $name)
    {
        $this->name = $name;
        $this->originalName = $originalName;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getOriginalName() : string
    {
        return $this->originalName;
    }
    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->array = \false;
        $this->type = $type;
        if ('[]' === \substr($type, -2)) {
            $this->array = \true;
            $this->type = \substr($type, 0, -2);
        }
    }
    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @param string $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function isArray() : bool
    {
        return $this->array;
    }
}
