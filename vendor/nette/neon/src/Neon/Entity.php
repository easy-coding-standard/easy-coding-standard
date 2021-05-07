<?php

namespace ECSPrefix20210507\Nette\Neon;

/**
 * Representation of NEON entity 'foo(bar=1)'
 */
final class Entity extends \stdClass
{
    /** @var mixed */
    public $value;
    /** @var array */
    public $attributes;
    public function __construct($value, array $attrs = [])
    {
        $this->value = $value;
        $this->attributes = $attrs;
    }
    public static function __set_state(array $properties)
    {
        return new self($properties['value'], $properties['attributes']);
    }
}
