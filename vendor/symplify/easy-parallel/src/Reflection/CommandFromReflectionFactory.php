<?php

declare (strict_types=1);
namespace ECSPrefix20211209\Symplify\EasyParallel\Reflection;

use ReflectionClass;
use ReflectionMethod;
use ECSPrefix20211209\Symfony\Component\Console\Command\Command;
use ECSPrefix20211209\Symplify\EasyParallel\Exception\ParallelShouldNotHappenException;
final class CommandFromReflectionFactory
{
    /**
     * @param class-string<Command> $className
     */
    public function create(string $className) : \ECSPrefix20211209\Symfony\Component\Console\Command\Command
    {
        $commandReflectionClass = new \ReflectionClass($className);
        $command = $commandReflectionClass->newInstanceWithoutConstructor();
        $parentClassReflection = $commandReflectionClass->getParentClass();
        if (!$parentClassReflection instanceof \ReflectionClass) {
            throw new \ECSPrefix20211209\Symplify\EasyParallel\Exception\ParallelShouldNotHappenException();
        }
        $parentConstructorReflectionMethod = $parentClassReflection->getConstructor();
        if (!$parentConstructorReflectionMethod instanceof \ReflectionMethod) {
            throw new \ECSPrefix20211209\Symplify\EasyParallel\Exception\ParallelShouldNotHappenException();
        }
        $parentConstructorReflectionMethod->invoke($command);
        return $command;
    }
}
