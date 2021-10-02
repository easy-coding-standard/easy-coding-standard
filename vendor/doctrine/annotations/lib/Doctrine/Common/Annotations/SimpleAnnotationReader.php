<?php

namespace ECSPrefix20211002\Doctrine\Common\Annotations;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
/**
 * Simple Annotation Reader.
 *
 * This annotation reader is intended to be used in projects where you have
 * full-control over all annotations that are available.
 *
 * @deprecated Deprecated in favour of using AnnotationReader
 */
class SimpleAnnotationReader implements \ECSPrefix20211002\Doctrine\Common\Annotations\Reader
{
    /** @var DocParser */
    private $parser;
    /**
     * Initializes a new SimpleAnnotationReader.
     */
    public function __construct()
    {
        $this->parser = new \ECSPrefix20211002\Doctrine\Common\Annotations\DocParser();
        $this->parser->setIgnoreNotImportedAnnotations(\true);
    }
    /**
     * Adds a namespace in which we will look for annotations.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function addNamespace($namespace)
    {
        $this->parser->addNamespace($namespace);
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionClass $class
     */
    public function getClassAnnotations($class)
    {
        return $this->parser->parse($class->getDocComment(), 'class ' . $class->getName());
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionMethod $method
     */
    public function getMethodAnnotations($method)
    {
        return $this->parser->parse($method->getDocComment(), 'method ' . $method->getDeclaringClass()->name . '::' . $method->getName() . '()');
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionProperty $property
     */
    public function getPropertyAnnotations($property)
    {
        return $this->parser->parse($property->getDocComment(), 'property ' . $property->getDeclaringClass()->name . '::$' . $property->getName());
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionClass $class
     */
    public function getClassAnnotation($class, $annotationName)
    {
        foreach ($this->getClassAnnotations($class) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }
        return null;
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionMethod $method
     */
    public function getMethodAnnotation($method, $annotationName)
    {
        foreach ($this->getMethodAnnotations($method) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }
        return null;
    }
    /**
     * {@inheritDoc}
     * @param \ReflectionProperty $property
     */
    public function getPropertyAnnotation($property, $annotationName)
    {
        foreach ($this->getPropertyAnnotations($property) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }
        return null;
    }
}
