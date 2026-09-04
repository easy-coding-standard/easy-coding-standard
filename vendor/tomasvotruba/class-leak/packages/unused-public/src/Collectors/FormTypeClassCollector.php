<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\ArrayItem;
use ECSPrefix202609\PhpParser\Node\Scalar\String_;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Type\Constant\ConstantStringType;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
/**
 * Match Symfony data_class element in forms types, as those use magic setters/getters
 * @implements Collector<ArrayItem, non-empty-array<string>|null>
 */
final class FormTypeClassCollector implements Collector
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Configuration
     */
    private $configuration;
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
    public function getNodeType(): string
    {
        return ArrayItem::class;
    }
    /**
     * @param ArrayItem $node
     * @return non-empty-array<string>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        if (!$node->key instanceof String_) {
            return null;
        }
        if ($node->key->value !== 'data_class') {
            return null;
        }
        $valueType = $scope->getType($node->value);
        if (!$valueType instanceof ConstantStringType) {
            return null;
        }
        return [$valueType->getValue()];
    }
}
