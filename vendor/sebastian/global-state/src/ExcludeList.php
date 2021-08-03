<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/global-state.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\GlobalState;

use function in_array;
use function strpos;
use ReflectionClass;
final class ExcludeList
{
    /**
     * @var array
     */
    private $globalVariables = [];
    /**
     * @var string[]
     */
    private $classes = [];
    /**
     * @var string[]
     */
    private $classNamePrefixes = [];
    /**
     * @var string[]
     */
    private $parentClasses = [];
    /**
     * @var string[]
     */
    private $interfaces = [];
    /**
     * @var array
     */
    private $staticAttributes = [];
    /**
     * @return void
     */
    public function addGlobalVariable(string $variableName)
    {
        $this->globalVariables[$variableName] = \true;
    }
    /**
     * @return void
     */
    public function addClass(string $className)
    {
        $this->classes[] = $className;
    }
    /**
     * @return void
     */
    public function addSubclassesOf(string $className)
    {
        $this->parentClasses[] = $className;
    }
    /**
     * @return void
     */
    public function addImplementorsOf(string $interfaceName)
    {
        $this->interfaces[] = $interfaceName;
    }
    /**
     * @return void
     */
    public function addClassNamePrefix(string $classNamePrefix)
    {
        $this->classNamePrefixes[] = $classNamePrefix;
    }
    /**
     * @return void
     */
    public function addStaticAttribute(string $className, string $attributeName)
    {
        if (!isset($this->staticAttributes[$className])) {
            $this->staticAttributes[$className] = [];
        }
        $this->staticAttributes[$className][$attributeName] = \true;
    }
    public function isGlobalVariableExcluded(string $variableName) : bool
    {
        return isset($this->globalVariables[$variableName]);
    }
    public function isStaticAttributeExcluded(string $className, string $attributeName) : bool
    {
        if (\in_array($className, $this->classes, \true)) {
            return \true;
        }
        foreach ($this->classNamePrefixes as $prefix) {
            if (\strpos($className, $prefix) === 0) {
                return \true;
            }
        }
        $class = new \ReflectionClass($className);
        foreach ($this->parentClasses as $type) {
            if ($class->isSubclassOf($type)) {
                return \true;
            }
        }
        foreach ($this->interfaces as $type) {
            if ($class->implementsInterface($type)) {
                return \true;
            }
        }
        if (isset($this->staticAttributes[$className][$attributeName])) {
            return \true;
        }
        return \false;
    }
}
