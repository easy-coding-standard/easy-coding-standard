<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\PhpParser\BuilderHelpers;
use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr;
use ConfigTransformer20210601\PhpParser\Node\Expr\ClassConstFetch;
use ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall;
use ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified;
use ConfigTransformer20210601\PhpParser\Node\Scalar\String_;
use ConfigTransformer20210601\Rector\NodeTypeResolver\Node\AttributeKey;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Configuration\SymfonyFunctionNameProvider;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ConstantNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\FunctionName;
final class StringExprResolver
{
    /**
     * @see https://regex101.com/r/laf2wR/1
     * @var string
     */
    const TWIG_HTML_XML_SUFFIX_REGEX = '#\\.(twig|html|xml)$#';
    /**
     * @var ConstantNodeFactory
     */
    private $constantNodeFactory;
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;
    /**
     * @var SymfonyFunctionNameProvider
     */
    private $symfonyFunctionNameProvider;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ConstantNodeFactory $constantNodeFactory, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory $commonNodeFactory, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Configuration\SymfonyFunctionNameProvider $symfonyFunctionNameProvider)
    {
        $this->constantNodeFactory = $constantNodeFactory;
        $this->commonNodeFactory = $commonNodeFactory;
        $this->symfonyFunctionNameProvider = $symfonyFunctionNameProvider;
    }
    public function resolve(string $value, bool $skipServiceReference, bool $skipClassesToConstantReference) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        if ($value === '') {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_($value);
        }
        $constFetch = $this->constantNodeFactory->createConstantIfValue($value);
        if ($constFetch !== null) {
            return $constFetch;
        }
        // do not print "\n" as empty space, but use string value instead
        if (\in_array($value, ["\r", "\n", "\r\n"], \true)) {
            return $this->keepNewline($value);
        }
        $value = \ltrim($value, '\\');
        if ($this->isClassType($value)) {
            return $this->resolveClassType($skipClassesToConstantReference, $value);
        }
        if (\ConfigTransformer20210601\Nette\Utils\Strings::startsWith($value, '@=')) {
            $value = \ltrim($value, '@=');
            $expr = $this->resolve($value, $skipServiceReference, $skipClassesToConstantReference);
            $args = [new \ConfigTransformer20210601\PhpParser\Node\Arg($expr)];
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall(new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\FunctionName::EXPR), $args);
        }
        // is service reference
        if (\ConfigTransformer20210601\Nette\Utils\Strings::startsWith($value, '@') && !$this->isFilePath($value)) {
            $refOrServiceFunctionName = $this->symfonyFunctionNameProvider->provideRefOrService();
            return $this->resolveServiceReferenceExpr($value, $skipServiceReference, $refOrServiceFunctionName);
        }
        return \ConfigTransformer20210601\PhpParser\BuilderHelpers::normalizeValue($value);
    }
    private function keepNewline(string $value) : \ConfigTransformer20210601\PhpParser\Node\Scalar\String_
    {
        $string = new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_($value);
        $string->setAttribute(\ConfigTransformer20210601\Rector\NodeTypeResolver\Node\AttributeKey::KIND, \ConfigTransformer20210601\PhpParser\Node\Scalar\String_::KIND_DOUBLE_QUOTED);
        return $string;
    }
    private function isFilePath(string $value) : bool
    {
        return (bool) \ConfigTransformer20210601\Nette\Utils\Strings::match($value, self::TWIG_HTML_XML_SUFFIX_REGEX);
    }
    /**
     * @return String_|ClassConstFetch
     */
    private function resolveClassType(bool $skipClassesToConstantReference, string $value) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        if ($skipClassesToConstantReference) {
            return new \ConfigTransformer20210601\PhpParser\Node\Scalar\String_($value);
        }
        return $this->commonNodeFactory->createClassReference($value);
    }
    private function isClassType(string $value) : bool
    {
        if (!\ctype_upper($value[0])) {
            return \false;
        }
        if (\class_exists($value)) {
            return \true;
        }
        return \interface_exists($value);
    }
    private function resolveServiceReferenceExpr(string $value, bool $skipServiceReference, string $functionName) : \ConfigTransformer20210601\PhpParser\Node\Expr
    {
        $value = \ltrim($value, '@');
        $expr = $this->resolve($value, $skipServiceReference, \false);
        if ($skipServiceReference) {
            return $expr;
        }
        $args = [new \ConfigTransformer20210601\PhpParser\Node\Arg($expr)];
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\FuncCall(new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified($functionName), $args);
    }
}
