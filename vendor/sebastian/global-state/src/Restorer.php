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

use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function function_exists;
use function get_defined_functions;
use function in_array;
use function is_array;
use ReflectionClass;
use ReflectionProperty;
/**
 * Restorer of snapshots of global state.
 */
class Restorer
{
    /**
     * Deletes function definitions that are not defined in a snapshot.
     *
     * @throws RuntimeException when the uopz_delete() function is not available
     *
     * @see https://github.com/krakjoe/uopz
     * @param \SebastianBergmann\GlobalState\Snapshot $snapshot
     * @return void
     */
    public function restoreFunctions($snapshot)
    {
        if (!\function_exists('ECSPrefix20210803\\uopz_delete')) {
            throw new \ECSPrefix20210803\SebastianBergmann\GlobalState\RuntimeException('The uopz_delete() function is required for this operation');
        }
        $functions = \get_defined_functions();
        foreach (\array_diff($functions['user'], $snapshot->functions()) as $function) {
            uopz_delete($function);
        }
    }
    /**
     * Restores all global and super-global variables from a snapshot.
     * @param \SebastianBergmann\GlobalState\Snapshot $snapshot
     * @return void
     */
    public function restoreGlobalVariables($snapshot)
    {
        $superGlobalArrays = $snapshot->superGlobalArrays();
        foreach ($superGlobalArrays as $superGlobalArray) {
            $this->restoreSuperGlobalArray($snapshot, $superGlobalArray);
        }
        $globalVariables = $snapshot->globalVariables();
        foreach (\array_keys($GLOBALS) as $key) {
            if ($key !== 'GLOBALS' && !\in_array($key, $superGlobalArrays, \true) && !$snapshot->excludeList()->isGlobalVariableExcluded($key)) {
                if (\array_key_exists($key, $globalVariables)) {
                    $GLOBALS[$key] = $globalVariables[$key];
                } else {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
    /**
     * Restores all static attributes in user-defined classes from this snapshot.
     * @param \SebastianBergmann\GlobalState\Snapshot $snapshot
     * @return void
     */
    public function restoreStaticAttributes($snapshot)
    {
        $current = new \ECSPrefix20210803\SebastianBergmann\GlobalState\Snapshot($snapshot->excludeList(), \false, \false, \false, \false, \true, \false, \false, \false, \false);
        $newClasses = \array_diff($current->classes(), $snapshot->classes());
        unset($current);
        foreach ($snapshot->staticAttributes() as $className => $staticAttributes) {
            foreach ($staticAttributes as $name => $value) {
                $reflector = new \ReflectionProperty($className, $name);
                $reflector->setAccessible(\true);
                $reflector->setValue($value);
            }
        }
        foreach ($newClasses as $className) {
            $class = new \ReflectionClass($className);
            $defaults = $class->getDefaultProperties();
            foreach ($class->getProperties() as $attribute) {
                if (!$attribute->isStatic()) {
                    continue;
                }
                $name = $attribute->getName();
                if ($snapshot->excludeList()->isStaticAttributeExcluded($className, $name)) {
                    continue;
                }
                if (!isset($defaults[$name])) {
                    continue;
                }
                $attribute->setAccessible(\true);
                $attribute->setValue($defaults[$name]);
            }
        }
    }
    /**
     * Restores a super-global variable array from this snapshot.
     * @return void
     */
    private function restoreSuperGlobalArray(\ECSPrefix20210803\SebastianBergmann\GlobalState\Snapshot $snapshot, string $superGlobalArray)
    {
        $superGlobalVariables = $snapshot->superGlobalVariables();
        if (isset($GLOBALS[$superGlobalArray]) && \is_array($GLOBALS[$superGlobalArray]) && isset($superGlobalVariables[$superGlobalArray])) {
            $keys = \array_keys(\array_merge($GLOBALS[$superGlobalArray], $superGlobalVariables[$superGlobalArray]));
            foreach ($keys as $key) {
                if (isset($superGlobalVariables[$superGlobalArray][$key])) {
                    $GLOBALS[$superGlobalArray][$key] = $superGlobalVariables[$superGlobalArray][$key];
                } else {
                    unset($GLOBALS[$superGlobalArray][$key]);
                }
            }
        }
    }
}
