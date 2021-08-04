<?php

namespace ECSPrefix20210804\DeepCopy\Matcher;

/**
 * @final
 */
class PropertyNameMatcher implements \ECSPrefix20210804\DeepCopy\Matcher\Matcher
{
    /**
     * @var string
     */
    private $property;
    /**
     * @param string $property Property name
     */
    public function __construct($property)
    {
        $this->property = $property;
    }
    /**
     * Matches a property by its name.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $property == $this->property;
    }
}
