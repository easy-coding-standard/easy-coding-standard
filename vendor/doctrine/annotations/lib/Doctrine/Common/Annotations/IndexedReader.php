<?php

namespace ECSPrefix20211002\Doctrine\Common\Annotations;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use function call_user_func_array;
use function get_class;
/**
 * Allows the reader to be used in-place of Doctrine's reader.
 */
class IndexedReader implements \ECSPrefix20211002\Doctrine\Common\Annotations\Reader
{
    /** @var Reader */
    private $delegate;
    public function __construct(\ECSPrefix20211002\Doctrine\Common\Annotations\Reader $reader)
    {
        $this->delegate = $reader;
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionClass $class
     */
    public function getClassAnnotations($class)
    {
        $annotations = [];
        foreach ($this->delegate->getClassAnnotations($class) as $annot) {
            $annotations[\get_class($annot)] = $annot;
        }
        return $annotations;
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionClass $class
     */
    public function getClassAnnotation($class, $annotation)
    {
        return $this->delegate->getClassAnnotation($class, $annotation);
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionMethod $method
     */
    public function getMethodAnnotations($method)
    {
        $annotations = [];
        foreach ($this->delegate->getMethodAnnotations($method) as $annot) {
            $annotations[\get_class($annot)] = $annot;
        }
        return $annotations;
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionMethod $method
     */
    public function getMethodAnnotation($method, $annotation)
    {
        return $this->delegate->getMethodAnnotation($method, $annotation);
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionProperty $property
     */
    public function getPropertyAnnotations($property)
    {
        $annotations = [];
        foreach ($this->delegate->getPropertyAnnotations($property) as $annot) {
            $annotations[\get_class($annot)] = $annot;
        }
        return $annotations;
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionProperty $property
     */
    public function getPropertyAnnotation($property, $annotation)
    {
        return $this->delegate->getPropertyAnnotation($property, $annotation);
    }
    /**
     * Proxies all methods to the delegate.
     *
     * @param string  $method
     * @param mixed[] $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return \call_user_func_array([$this->delegate, $method], $args);
    }
}
