<?php

namespace ECSPrefix20211002\Doctrine\Common\Annotations;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
/**
 * Interface for annotation readers.
 */
interface Reader
{
    /**
     * Gets the annotations applied to a class.
     *
     * @param ReflectionClass $class The ReflectionClass of the class from which
     * the class annotations should be read.
     *
     * @return array<object> An array of Annotations.
     */
    public function getClassAnnotations($class);
    /**
     * Gets a class annotation.
     *
     * @param ReflectionClass $class          The ReflectionClass of the class from which
     *          the class annotations should be read.
     * @param class-string<T> $annotationName The name of the annotation.
     *
     * @return T|null The Annotation or NULL, if the requested annotation does not exist.
     *
     * @template T
     */
    public function getClassAnnotation($class, $annotationName);
    /**
     * Gets the annotations applied to a method.
     *
     * @param ReflectionMethod $method The ReflectionMethod of the method from which
     * the annotations should be read.
     *
     * @return array<object> An array of Annotations.
     */
    public function getMethodAnnotations($method);
    /**
     * Gets a method annotation.
     *
     * @param ReflectionMethod $method         The ReflectionMethod to read the annotations from.
     * @param class-string<T>  $annotationName The name of the annotation.
     *
     * @return T|null The Annotation or NULL, if the requested annotation does not exist.
     *
     * @template T
     */
    public function getMethodAnnotation($method, $annotationName);
    /**
     * Gets the annotations applied to a property.
     *
     * @param ReflectionProperty $property The ReflectionProperty of the property
     * from which the annotations should be read.
     *
     * @return array<object> An array of Annotations.
     */
    public function getPropertyAnnotations($property);
    /**
     * Gets a property annotation.
     *
     * @param ReflectionProperty $property       The ReflectionProperty to read the annotations from.
     * @param class-string<T>    $annotationName The name of the annotation.
     *
     * @return T|null The Annotation or NULL, if the requested annotation does not exist.
     *
     * @template T
     */
    public function getPropertyAnnotation($property, $annotationName);
}
