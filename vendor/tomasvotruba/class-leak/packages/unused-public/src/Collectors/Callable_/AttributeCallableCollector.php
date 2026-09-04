<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\Callable_;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Attribute;
use ECSPrefix202609\PhpParser\Node\AttributeGroup;
use ECSPrefix202609\PhpParser\Node\Expr\Array_;
use ECSPrefix202609\PhpParser\Node\Scalar\String_;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Type\Constant\ConstantStringType;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ValueObject\ClassAndMethodArrayExprs;
/**
 * @implements Collector<AttributeGroup, non-empty-array<string>|null>
 */
final class AttributeCallableCollector implements Collector
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
        return AttributeGroup::class;
    }
    /**
     * @param AttributeGroup $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        foreach ($node->attrs as $attr) {
            $attributeClasName = $attr->name->toString();
            if ($attributeClasName !== 'Symfony\Component\Validator\Constraints\Callback') {
                continue;
            }
            $classAndMethodArrayExprs = $this->matchClassAndMethodExprs($attr);
            if (!$classAndMethodArrayExprs instanceof ClassAndMethodArrayExprs) {
                continue;
            }
            $classType = $scope->getType($classAndMethodArrayExprs->getClassExpr());
            if ($classType instanceof ConstantStringType) {
                $className = $classType->getValue();
            } else {
                continue;
            }
            $methodExpr = $classAndMethodArrayExprs->getMethodExpr();
            if ($methodExpr instanceof String_) {
                $methodName = $methodExpr->value;
            } else {
                continue;
            }
            return [$className . '::' . $methodName];
        }
        return null;
    }
    private function matchClassAndMethodExprs(Attribute $attribute): ?ClassAndMethodArrayExprs
    {
        $firstArg = $attribute->args[0];
        if (!$firstArg->value instanceof Array_) {
            return null;
        }
        $array = $firstArg->value;
        if (count($array->items) !== 2) {
            return null;
        }
        $classArrayItem = $array->items[0]->value;
        $methodArrayItem = $array->items[1]->value;
        return new ClassAndMethodArrayExprs($classArrayItem, $methodArrayItem);
    }
}
