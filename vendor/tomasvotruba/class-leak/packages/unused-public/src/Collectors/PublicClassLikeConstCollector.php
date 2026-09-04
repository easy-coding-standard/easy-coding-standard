<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Stmt\ClassConst;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ApiDocStmtAnalyzer;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * @implements Collector<ClassConst, non-empty-array<array{class-string, string, int}>|null>
 */
final class PublicClassLikeConstCollector implements Collector
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\ApiDocStmtAnalyzer
     */
    private $apiDocStmtAnalyzer;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Configuration
     */
    private $configuration;
    public function __construct(ApiDocStmtAnalyzer $apiDocStmtAnalyzer, Configuration $configuration)
    {
        $this->apiDocStmtAnalyzer = $apiDocStmtAnalyzer;
        $this->configuration = $configuration;
    }
    public function getNodeType(): string
    {
        return ClassConst::class;
    }
    /**
     * @param ClassConst $node
     * @return non-empty-array<array{class-string, string, int}>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->isUnusedConstantsEnabled()) {
            return null;
        }
        if (!$node->isPublic()) {
            return null;
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return null;
        }
        if ($this->apiDocStmtAnalyzer->isApiDoc($node, $classReflection)) {
            return null;
        }
        $constantNames = [];
        foreach ($node->consts as $constConst) {
            $constantNames[] = [$classReflection->getName(), $constConst->name->toString(), $node->getLine()];
        }
        if ($constantNames === []) {
            return null;
        }
        return $constantNames;
    }
}
