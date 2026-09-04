<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Expr\PropertyFetch;
use ECSPrefix202609\PhpParser\Node\Expr\Variable;
use ECSPrefix202609\PhpParser\Node\Identifier;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\PHPStan\Type\TypeCombinator;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassTypeDetector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * @implements Collector<PropertyFetch, non-empty-array<string>|null>
 */
final class PublicPropertyFetchCollector implements Collector
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Configuration
     */
    private $configuration;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\ClassTypeDetector
     */
    private $classTypeDetector;
    public function __construct(Configuration $configuration, ClassTypeDetector $classTypeDetector)
    {
        $this->configuration = $configuration;
        $this->classTypeDetector = $classTypeDetector;
    }
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }
    /**
     * @param PropertyFetch $node
     * @return non-empty-array<string>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->isUnusedPropertyEnabled()) {
            return null;
        }
        // skip local
        if ($node->var instanceof Variable && $node->var->name === 'this') {
            return null;
        }
        if (!$node->name instanceof Identifier) {
            return null;
        }
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && $this->classTypeDetector->isTestClass($classReflection)) {
            return null;
        }
        $result = [];
        $propertyFetcherType = $scope->getType($node->var);
        $propertyFetcherType = TypeCombinator::removeNull($propertyFetcherType);
        foreach ($propertyFetcherType->getObjectClassReflections() as $classReflection) {
            $propertyName = $node->name->toString();
            if (!$classReflection->hasProperty($propertyName)) {
                continue;
            }
            $propertyReflection = $classReflection->getProperty($propertyName, $scope);
            $result[] = $propertyReflection->getDeclaringClass()->getName() . '::' . $propertyName;
        }
        if ($result === []) {
            return null;
        }
        return $result;
    }
}
