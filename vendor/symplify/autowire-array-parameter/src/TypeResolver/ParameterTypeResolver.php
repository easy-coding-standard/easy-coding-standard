<?php

namespace Symplify\AutowireArrayParameter\TypeResolver;

use ECSPrefix20210513\Nette\Utils\Reflection;
use ReflectionMethod;
use Symplify\AutowireArrayParameter\DocBlock\ParamTypeDocBlockResolver;
final class ParameterTypeResolver
{
    /**
     * @var ParamTypeDocBlockResolver
     */
    private $paramTypeDocBlockResolver;
    /**
     * @var array<string, string>
     */
    private $resolvedParameterTypesCached = [];
    public function __construct(\Symplify\AutowireArrayParameter\DocBlock\ParamTypeDocBlockResolver $paramTypeDocBlockResolver)
    {
        $this->paramTypeDocBlockResolver = $paramTypeDocBlockResolver;
    }
    /**
     * @return string|null
     * @param string $parameterName
     */
    public function resolveParameterType($parameterName, \ReflectionMethod $reflectionMethod)
    {
        $parameterName = (string) $parameterName;
        $docComment = $reflectionMethod->getDocComment();
        if ($docComment === \false) {
            return null;
        }
        $declaringReflectionClass = $reflectionMethod->getDeclaringClass();
        $uniqueKey = $parameterName . $declaringReflectionClass->getName() . $reflectionMethod->getName();
        if (isset($this->resolvedParameterTypesCached[$uniqueKey])) {
            return $this->resolvedParameterTypesCached[$uniqueKey];
        }
        $resolvedType = $this->paramTypeDocBlockResolver->resolve($docComment, $parameterName);
        if ($resolvedType === null) {
            return null;
        }
        // not a class|interface type
        if (\ctype_lower($resolvedType[0])) {
            return null;
        }
        $resolvedClass = \ECSPrefix20210513\Nette\Utils\Reflection::expandClassName($resolvedType, $declaringReflectionClass);
        $this->resolvedParameterTypesCached[$uniqueKey] = $resolvedClass;
        return $resolvedClass;
    }
}
