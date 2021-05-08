<?php

namespace Symplify\PackageBuilder\Reflection;

use ReflectionClass;
final class ClassLikeExistenceChecker
{
    /**
     * @var string[]
     */
    private $sensitiveExistingClasses = [];
    /**
     * @var string[]
     */
    private $sensitiveNonExistingClasses = [];
    /**
     * @param string $classLike
     * @return bool
     */
    public function doesClassLikeExist($classLike)
    {
        if (\is_object($classLike)) {
            $classLike = (string) $classLike;
        }
        if (\class_exists($classLike)) {
            return \true;
        }
        if (\interface_exists($classLike)) {
            return \true;
        }
        return \trait_exists($classLike);
    }
    /**
     * @param string $classLikeName
     * @return bool
     */
    public function doesClassLikeInsensitiveExists($classLikeName)
    {
        if (\is_object($classLikeName)) {
            $classLikeName = (string) $classLikeName;
        }
        if (!$this->doesClassLikeExist($classLikeName)) {
            return \false;
        }
        // already known values
        if (\in_array($classLikeName, $this->sensitiveExistingClasses, \true)) {
            return \true;
        }
        if (\in_array($classLikeName, $this->sensitiveNonExistingClasses, \true)) {
            return \false;
        }
        $reflectionClass = new \ReflectionClass($classLikeName);
        if ($classLikeName !== $reflectionClass->getName()) {
            $this->sensitiveNonExistingClasses[] = $classLikeName;
            return \false;
        }
        $this->sensitiveExistingClasses[] = $classLikeName;
        return \true;
    }
}
