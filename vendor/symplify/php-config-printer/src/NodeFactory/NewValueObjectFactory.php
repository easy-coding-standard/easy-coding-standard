<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory;

use ConfigTransformer20210601\PhpParser\BuilderHelpers;
use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr\Array_;
use ConfigTransformer20210601\PhpParser\Node\Expr\New_;
use ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified;
use ReflectionClass;
final class NewValueObjectFactory
{
    /**
     * @param object $valueObject
     */
    public function create($valueObject) : \ConfigTransformer20210601\PhpParser\Node\Expr\New_
    {
        $valueObjectClass = \get_class($valueObject);
        $propertyValues = $this->resolvePropertyValuesFromValueObject($valueObjectClass, $valueObject);
        $args = $this->createArgs($propertyValues);
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\New_(new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified($valueObjectClass), $args);
    }
    /**
     * @return mixed[]
     * @param object $valueObject
     */
    private function resolvePropertyValuesFromValueObject(string $valueObjectClass, $valueObject) : array
    {
        $reflectionClass = new \ReflectionClass($valueObjectClass);
        $propertyValues = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $reflectionProperty->setAccessible(\true);
            $propertyValues[] = $reflectionProperty->getValue($valueObject);
        }
        return $propertyValues;
    }
    /**
     * @param mixed[] $propertyValues
     * @return Arg[]
     */
    private function createArgs(array $propertyValues) : array
    {
        $args = [];
        foreach ($propertyValues as $propertyValue) {
            if (\is_object($propertyValue)) {
                $args[] = new \ConfigTransformer20210601\PhpParser\Node\Arg($resolvedNestedObject = $this->create($propertyValue));
            } elseif (\is_array($propertyValue)) {
                $args[] = new \ConfigTransformer20210601\PhpParser\Node\Arg(new \ConfigTransformer20210601\PhpParser\Node\Expr\Array_($this->createArgs($propertyValue)));
            } else {
                $args[] = new \ConfigTransformer20210601\PhpParser\Node\Arg(\ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeValue($propertyValue));
            }
        }
        return $args;
    }
}
