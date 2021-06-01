<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory;

use ConfigTransformer20210601\PhpParser\BuilderHelpers;
use ConfigTransformer20210601\PhpParser\Node\Expr;
use ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat;
use ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch;
use ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch;
use ConfigTransformer20210601\PhpParser\Node\Name;
use ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified;
use ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst\Dir;
use ConfigTransformer20210601\PhpParser\Node\Scalar\String_;
final class CommonNodeFactory
{
    public function createAbsoluteDirExpr($argument) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        if ($argument === '') {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_('');
        }
        if (\is_string($argument)) {
            // preslash with dir
            $argument = '/' . $argument;
        }
        $argumentValue = \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeValue($argument);
        if ($argumentValue instanceof \ConfigTransformer20210601\PhpParser\Node\Scalar\String_) {
            $argumentValue = new \ConfigTransformer20210601\PhpParser\Node\Expr\BinaryOp\Concat(new \ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst\Dir(), $argumentValue);
        }
        return $argumentValue;
    }
    public function createClassReference(string $className) : \ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch
    {
        return $this->createConstFetch($className, 'class');
    }
    public function createConstFetch(string $className, string $constantName) : \ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch(new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified($className), $constantName);
    }
    public function createFalse() : \ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch
    {
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\ConstFetch(new \ConfigTransformer20210601\PhpParser\Node\Name('false'));
    }
}
