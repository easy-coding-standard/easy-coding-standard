<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver;

use ConfigTransformer20210601\PhpParser\Node\Expr\Array_;
use ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem;
use ConfigTransformer20210601\Symfony\Component\Yaml\Tag\TaggedValue;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Configuration\SymfonyFunctionNameProvider;
final class TaggedReturnsCloneResolver
{
    /**
     * @var ServiceReferenceExprResolver
     */
    private $serviceReferenceExprResolver;
    /**
     * @var SymfonyFunctionNameProvider
     */
    private $symfonyFunctionNameProvider;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Configuration\SymfonyFunctionNameProvider $symfonyFunctionNameProvider, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ExprResolver\ServiceReferenceExprResolver $serviceReferenceExprResolver)
    {
        $this->serviceReferenceExprResolver = $serviceReferenceExprResolver;
        $this->symfonyFunctionNameProvider = $symfonyFunctionNameProvider;
    }
    public function resolve(\ConfigTransformer20210601\Symfony\Component\Yaml\Tag\TaggedValue $taggedValue) : \ConfigTransformer20210601\PhpParser\Node\Expr\Array_
    {
        $serviceName = $taggedValue->getValue()[0];
        $functionName = $this->symfonyFunctionNameProvider->provideRefOrService();
        $funcCall = $this->serviceReferenceExprResolver->resolveServiceReferenceExpr($serviceName, \false, $functionName);
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\Array_([new \ConfigTransformer20210601\PhpParser\Node\Expr\ArrayItem($funcCall)]);
    }
}
