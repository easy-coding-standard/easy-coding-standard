<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\StaticCall;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Expr\StaticCall;
use ECSPrefix202609\PhpParser\Node\Identifier;
use ECSPrefix202609\PhpParser\Node\Name;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassTypeDetector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * @implements Collector<StaticCall, non-empty-array<string>|null>
 */
final class StaticMethodCallCollector implements Collector
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
    public function getNodeType(): string
    {
        return StaticCall::class;
    }
    /**
     * @param StaticCall $node
     * @return non-empty-array<string>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        if (!$node->name instanceof Identifier) {
            return null;
        }
        if (!$node->class instanceof Name) {
            return null;
        }
        // skip calls in tests, as they are not used in production
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && $this->classTypeDetector->isTestClass($classReflection)) {
            return null;
        }
        return [$node->class->toString() . '::' . $node->name->toString()];
    }
}
