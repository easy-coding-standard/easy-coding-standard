<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\DependencyInjection\Argument;

/**
 * Represents a collection of services found by tag name to lazily iterate over.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
class TaggedIteratorArgument extends \ECSPrefix20210507\Symfony\Component\DependencyInjection\Argument\IteratorArgument
{
    private $tag;
    private $indexAttribute;
    private $defaultIndexMethod;
    private $defaultPriorityMethod;
    private $needsIndexes = \false;
    /**
     * @param string      $tag                   The name of the tag identifying the target services
     * @param string $indexAttribute The name of the attribute that defines the key referencing each service in the tagged collection
     * @param string $defaultIndexMethod The static method that should be called to get each service's key when their tag doesn't define the previous attribute
     * @param bool        $needsIndexes          Whether indexes are required and should be generated when computing the map
     * @param string $defaultPriorityMethod The static method that should be called to get each service's priority when their tag doesn't define the "priority" attribute
     */
    public function __construct($tag, $indexAttribute = null, $defaultIndexMethod = null, $needsIndexes = \false, $defaultPriorityMethod = null)
    {
        parent::__construct([]);
        if (null === $indexAttribute && $needsIndexes) {
            $indexAttribute = \preg_match('/[^.]++$/', $tag, $m) ? $m[0] : $tag;
        }
        $this->tag = $tag;
        $this->indexAttribute = $indexAttribute;
        $this->defaultIndexMethod = $defaultIndexMethod ?: ($indexAttribute ? 'getDefault' . \str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $indexAttribute))) . 'Name' : null);
        $this->needsIndexes = $needsIndexes;
        $this->defaultPriorityMethod = $defaultPriorityMethod ?: ($indexAttribute ? 'getDefault' . \str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $indexAttribute))) . 'Priority' : null);
    }
    public function getTag()
    {
        return $this->tag;
    }
    /**
     * @return string|null
     */
    public function getIndexAttribute()
    {
        return $this->indexAttribute;
    }
    /**
     * @return string|null
     */
    public function getDefaultIndexMethod()
    {
        return $this->defaultIndexMethod;
    }
    /**
     * @return bool
     */
    public function needsIndexes()
    {
        return $this->needsIndexes;
    }
    /**
     * @return string|null
     */
    public function getDefaultPriorityMethod()
    {
        return $this->defaultPriorityMethod;
    }
}
