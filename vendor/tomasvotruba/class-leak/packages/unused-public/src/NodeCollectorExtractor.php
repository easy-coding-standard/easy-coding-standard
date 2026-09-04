<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic;

use ECSPrefix202609\PHPStan\Node\CollectedDataNode;
use ECSPrefix202609\TomasVotruba\UnusedPublic\CollectorMapper\MethodCallCollectorMapper;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\Callable_\AttributeCallableCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\Callable_\CallableTypeCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\MethodCall\MethodCallableCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\MethodCall\MethodCallCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\StaticCall\StaticMethodCallableCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\StaticCall\StaticMethodCallCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ValueObject\LocalAndExternalMethodCallReferences;
final class NodeCollectorExtractor
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\CollectorMapper\MethodCallCollectorMapper
     */
    private $methodCallCollectorMapper;
    public function __construct(MethodCallCollectorMapper $methodCallCollectorMapper)
    {
        $this->methodCallCollectorMapper = $methodCallCollectorMapper;
    }
    public function extractLocalAndExternalMethodCallReferences(CollectedDataNode $collectedDataNode): LocalAndExternalMethodCallReferences
    {
        $collectedDatas = $this->extractCollectedDatas($collectedDataNode);
        return $this->methodCallCollectorMapper->mapToLocalAndExternal($collectedDatas);
    }
    /**
     * @return string[]
     */
    public function extractMethodCallReferences(CollectedDataNode $collectedDataNode): array
    {
        $collectedDatas = $this->extractCollectedDatas($collectedDataNode);
        return $this->methodCallCollectorMapper->mapToMethodCallReferences($collectedDatas);
    }
    /**
     * @return array<int, array<string, list<(non-empty-array<string>|null)>>>
     */
    private function extractCollectedDatas(CollectedDataNode $collectedDataNode): array
    {
        return [$collectedDataNode->get(MethodCallCollector::class), $collectedDataNode->get(MethodCallableCollector::class), $collectedDataNode->get(StaticMethodCallCollector::class), $collectedDataNode->get(StaticMethodCallableCollector::class), $collectedDataNode->get(AttributeCallableCollector::class), $collectedDataNode->get(CallableTypeCollector::class)];
    }
}
