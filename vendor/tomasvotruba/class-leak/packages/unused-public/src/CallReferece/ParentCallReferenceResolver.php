<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\CallReferece;

use ECSPrefix202609\PHPStan\Reflection\ReflectionProvider;
final class ParentCallReferenceResolver
{
    /**
     * @readonly
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }
    /**
     * @return string[]
     */
    public function findParentClassMethodReferences(string $className, string $methodName): array
    {
        if (!$this->reflectionProvider->hasClass($className)) {
            return [];
        }
        $classReflection = $this->reflectionProvider->getClass($className);
        $classMethodReferences = [];
        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasNativeMethod($methodName)) {
                $classMethodReferences[] = $parentClassReflection->getName() . '::' . $methodName;
            }
        }
        return $classMethodReferences;
    }
}
