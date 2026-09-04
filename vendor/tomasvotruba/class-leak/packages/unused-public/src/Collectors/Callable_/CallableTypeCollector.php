<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\Callable_;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Expr;
use ECSPrefix202609\PhpParser\Node\Expr\Array_;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\PHPStan\Type\Constant\ConstantArrayType;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ClassTypeDetector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * @implements Collector<Expr\Array_, non-empty-array<string>|null>
 */
final class CallableTypeCollector implements Collector
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
        return Array_::class;
    }
    /**
     * @param Expr\Array_ $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        // skip calls in tests, as they are not used in production
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && $this->classTypeDetector->isTestClass($classReflection)) {
            return null;
        }
        $callableType = $scope->getType($node);
        if (!$callableType instanceof ConstantArrayType) {
            return null;
        }
        $classMethodReferences = [];
        foreach ($callableType->getConstantArrays() as $constantArrayType) {
            $typeAndMethodNames = $constantArrayType->findTypeAndMethodNames();
            if ($typeAndMethodNames === []) {
                continue;
            }
            foreach ($typeAndMethodNames as $typeAndMethodName) {
                if ($typeAndMethodName->isUnknown()) {
                    continue;
                }
                $objectClassNames = $typeAndMethodName->getType()->getObjectClassNames();
                foreach ($objectClassNames as $objectClassName) {
                    $classMethodReferences[] = $objectClassName . '::' . $typeAndMethodName->getMethod();
                }
            }
        }
        return $classMethodReferences;
    }
}
