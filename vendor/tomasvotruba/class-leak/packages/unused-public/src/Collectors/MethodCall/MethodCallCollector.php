<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\MethodCall;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Expr;
use ECSPrefix202609\PhpParser\Node\Expr\MethodCall;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\TomasVotruba\UnusedPublic\CallReferece\CallReferencesFlatter;
use ECSPrefix202609\TomasVotruba\UnusedPublic\CallReferece\ParentCallReferenceResolver;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassMethodCallReferenceResolver;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassTypeDetector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * @implements Collector<MethodCall, non-empty-array<string>|null>
 */
final class MethodCallCollector implements Collector
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\CallReferece\ParentCallReferenceResolver
     */
    private $parentCallReferenceResolver;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\ClassMethodCallReferenceResolver
     */
    private $classMethodCallReferenceResolver;
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
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\CallReferece\CallReferencesFlatter
     */
    private $callReferencesFlatter;
    public function __construct(ParentCallReferenceResolver $parentCallReferenceResolver, ClassMethodCallReferenceResolver $classMethodCallReferenceResolver, Configuration $configuration, ClassTypeDetector $classTypeDetector, CallReferencesFlatter $callReferencesFlatter)
    {
        $this->parentCallReferenceResolver = $parentCallReferenceResolver;
        $this->classMethodCallReferenceResolver = $classMethodCallReferenceResolver;
        $this->configuration = $configuration;
        $this->classTypeDetector = $classTypeDetector;
        $this->callReferencesFlatter = $callReferencesFlatter;
    }
    public function getNodeType(): string
    {
        return MethodCall::class;
    }
    /**
     * @param MethodCall $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        // unable to resolve method name
        if ($node->name instanceof Expr) {
            return null;
        }
        // skip calls in tests, as they are not used in production
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && $this->classTypeDetector->isTestClass($classReflection)) {
            return null;
        }
        $classMethodCallReferences = $this->classMethodCallReferenceResolver->resolve($node, $scope);
        $classMethodReferences = $this->callReferencesFlatter->flatten($classMethodCallReferences);
        foreach ($classMethodCallReferences as $classMethodCallReference) {
            $parentClassMethodReferences = $this->parentCallReferenceResolver->findParentClassMethodReferences($classMethodCallReference->getClass(), $classMethodCallReference->getMethod());
            foreach ($parentClassMethodReferences as $parentClassMethodReference) {
                $classMethodReferences[] = $parentClassMethodReference;
            }
        }
        return $classMethodReferences;
    }
}
