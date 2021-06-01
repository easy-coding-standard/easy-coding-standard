<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory;

use ConfigTransformer20210601\PhpParser\BuilderHelpers;
use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr;
use ConfigTransformer20210601\PhpParser\Node\Expr\Array_;
use ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem;
use ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall;
use ConfigTransformer20210601\PhpParser\Node\Name;
use ConfigTransformer20210601\Symfony\Component\Yaml\Tag\TaggedValue;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Exception\NotImplementedYetException;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\StringExprResolver;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\TaggedReturnsCloneResolver;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\TaggedServiceResolver;
final class ArgsNodeFactory
{
    /**
     * @var string
     */
    const TAG_SERVICE = 'service';
    /**
     * @var string
     */
    const TAG_RETURNS_CLONE = 'returns_clone';
    /**
     * @var StringExprResolver
     */
    private $stringExprResolver;
    /**
     * @var TaggedReturnsCloneResolver
     */
    private $taggedReturnsCloneResolver;
    /**
     * @var TaggedServiceResolver
     */
    private $taggedServiceResolver;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\StringExprResolver $stringExprResolver, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\TaggedReturnsCloneResolver $taggedReturnsCloneResolver, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\TaggedServiceResolver $taggedServiceResolver)
    {
        $this->stringExprResolver = $stringExprResolver;
        $this->taggedReturnsCloneResolver = $taggedReturnsCloneResolver;
        $this->taggedServiceResolver = $taggedServiceResolver;
    }
    /**
     * @return Arg[]
     */
    public function createFromValuesAndWrapInArray($values) : array
    {
        if (\is_array($values)) {
            $array = $this->resolveExprFromArray($values);
        } else {
            $expr = $this->resolveExpr($values);
            $items = [new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem($expr)];
            $array = new \ConfigTransformer20210601\PhpParser\Node\Expr\Array_($items);
        }
        return [new \ConfigTransformer20210601\PhpParser\Node\Arg($array)];
    }
    /**
     * @return Arg[]
     */
    public function createFromValues($values, bool $skipServiceReference = \false, bool $skipClassesToConstantReference = \false) : array
    {
        if (\is_array($values)) {
            $args = [];
            foreach ($values as $value) {
                $expr = $this->resolveExpr($value, $skipServiceReference, $skipClassesToConstantReference);
                $args[] = new \ConfigTransformer20210601\PhpParser\Node\Arg($expr);
            }
            return $args;
        }
        if ($values instanceof \ConfigTransformer20210601\PhpParser\Node) {
            if ($values instanceof \ConfigTransformer20210601\PhpParser\Node\Arg) {
                return [$values];
            }
            if ($values instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
                return [new \ConfigTransformer20210601\PhpParser\Node\Arg($values)];
            }
        }
        if (\is_string($values)) {
            $expr = $this->resolveExpr($values);
            return [new \ConfigTransformer20210601\PhpParser\Node\Arg($expr)];
        }
        throw new \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Exception\NotImplementedYetException();
    }
    public function resolveExpr($value, bool $skipServiceReference = \false, bool $skipClassesToConstantReference = \false) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        if (\is_string($value)) {
            return $this->stringExprResolver->resolve($value, $skipServiceReference, $skipClassesToConstantReference);
        }
        if ($value instanceof \ConfigTransformer20210601\PhpParser\Node\Expr) {
            return $value;
        }
        if ($value instanceof \ConfigTransformer20210601\Symfony\Component\Yaml\Tag\TaggedValue) {
            return $this->createServiceReferenceFromTaggedValue($value);
        }
        if (\is_array($value)) {
            $arrayItems = $this->resolveArrayItems($value, $skipClassesToConstantReference);
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\Array_($arrayItems);
        }
        return \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeValue($value);
    }
    private function resolveExprFromArray(array $values) : \ConfigTransformer20210601\PhpParser\Node\Expr\Array_
    {
        $arrayItems = [];
        foreach ($values as $key => $value) {
            $expr = \is_array($value) ? $this->resolveExprFromArray($value) : $this->resolveExpr($value);
            if (!\is_int($key)) {
                $keyExpr = $this->resolveExpr($key);
                $arrayItem = new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem($expr, $keyExpr);
            } else {
                $arrayItem = new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem($expr);
            }
            $arrayItems[] = $arrayItem;
        }
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\Array_($arrayItems);
    }
    private function createServiceReferenceFromTaggedValue(\ConfigTransformer20210601\Symfony\Component\Yaml\Tag\TaggedValue $taggedValue) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        // that's the only value
        if ($taggedValue->getTag() === self::TAG_RETURNS_CLONE) {
            return $this->taggedReturnsCloneResolver->resolve($taggedValue);
        }
        if ($taggedValue->getTag() === self::TAG_SERVICE) {
            return $this->taggedServiceResolver->resolve($taggedValue);
        }
        $args = $this->createFromValues($taggedValue->getValue());
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall(new \ConfigTransformer20210601\PhpParser\Node\Name($taggedValue->getTag()), $args);
    }
    /**
     * @param mixed[] $value
     * @return ArrayItem[]
     */
    private function resolveArrayItems(array $value, bool $skipClassesToConstantReference) : array
    {
        $arrayItems = [];
        $naturalKey = 0;
        foreach ($value as $nestedKey => $nestedValue) {
            $valueExpr = $this->resolveExpr($nestedValue, \false, $skipClassesToConstantReference);
            if (!\is_int($nestedKey) || $nestedKey !== $naturalKey) {
                $keyExpr = $this->resolveExpr($nestedKey, \false, $skipClassesToConstantReference);
                $arrayItem = new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem($valueExpr, $keyExpr);
            } else {
                $arrayItem = new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem($valueExpr);
            }
            $arrayItems[] = $arrayItem;
            ++$naturalKey;
        }
        return $arrayItems;
    }
}
