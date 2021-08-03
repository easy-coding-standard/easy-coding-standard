<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/object-enumerator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\ObjectEnumerator;

use function array_merge;
use function func_get_args;
use function is_array;
use function is_object;
use ECSPrefix20210803\SebastianBergmann\ObjectReflector\ObjectReflector;
use ECSPrefix20210803\SebastianBergmann\RecursionContext\Context;
/**
 * Traverses array structures and object graphs
 * to enumerate all referenced objects.
 */
class Enumerator
{
    /**
     * Returns an array of all objects referenced either
     * directly or indirectly by a variable.
     *
     * @param array|object $variable
     *
     * @return object[]
     */
    public function enumerate($variable)
    {
        if (!\is_array($variable) && !\is_object($variable)) {
            throw new \ECSPrefix20210803\SebastianBergmann\ObjectEnumerator\InvalidArgumentException();
        }
        if (isset(\func_get_args()[1])) {
            if (!\func_get_args()[1] instanceof \ECSPrefix20210803\SebastianBergmann\RecursionContext\Context) {
                throw new \ECSPrefix20210803\SebastianBergmann\ObjectEnumerator\InvalidArgumentException();
            }
            $processed = \func_get_args()[1];
        } else {
            $processed = new \ECSPrefix20210803\SebastianBergmann\RecursionContext\Context();
        }
        $objects = [];
        if ($processed->contains($variable)) {
            return $objects;
        }
        $array = $variable;
        $processed->add($variable);
        if (\is_array($variable)) {
            foreach ($array as $element) {
                if (!\is_array($element) && !\is_object($element)) {
                    continue;
                }
                $objects = \array_merge($objects, $this->enumerate($element, $processed));
            }
        } else {
            $objects[] = $variable;
            $reflector = new \ECSPrefix20210803\SebastianBergmann\ObjectReflector\ObjectReflector();
            foreach ($reflector->getAttributes($variable) as $value) {
                if (!\is_array($value) && !\is_object($value)) {
                    continue;
                }
                $objects = \array_merge($objects, $this->enumerate($value, $processed));
            }
        }
        return $objects;
    }
}
