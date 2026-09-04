<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\MethodCall;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Expr;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Node\MethodCallableNode;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\TomasVotruba\UnusedPublic\CallReferece\CallReferencesFlatter;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassMethodCallReferenceResolver;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassTypeDetector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * @implements Collector<MethodCallableNode, non-empty-array<string>|null>
 */
final class MethodCallableCollector implements Collector
{
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
    public function __construct(ClassMethodCallReferenceResolver $classMethodCallReferenceResolver, Configuration $configuration, ClassTypeDetector $classTypeDetector, CallReferencesFlatter $callReferencesFlatter)
    {
        $this->classMethodCallReferenceResolver = $classMethodCallReferenceResolver;
        $this->configuration = $configuration;
        $this->classTypeDetector = $classTypeDetector;
        $this->callReferencesFlatter = $callReferencesFlatter;
    }
    public function getNodeType(): string
    {
        return MethodCallableNode::class;
    }
    /**
     * @param MethodCallableNode $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        // unable to resolve method name
        if ($node->getName() instanceof Expr) {
            return null;
        }
        // skip calls in tests, as they are not used in production
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && $this->classTypeDetector->isTestClass($classReflection)) {
            return null;
        }
        $classMethodCallReferences = $this->classMethodCallReferenceResolver->resolve($node->getOriginalNode(), $scope);
        return $this->callReferencesFlatter->flatten($classMethodCallReferences);
    }
}
