<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\RoutingCaseConverter;

use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
use ConfigTransformer20210601\PhpParser\Node\Expr\Variable;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Expression;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName;
final class PathRoutingCaseConverter implements \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface
{
    /**
     * @var string[]
     */
    const NESTED_KEYS = ['controller', 'defaults', self::METHODS, 'requirements'];
    /**
     * @var string
     */
    const PATH = 'path';
    /**
     * @var string
     */
    const METHODS = 'methods';
    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory $argsNodeFactory)
    {
        $this->argsNodeFactory = $argsNodeFactory;
    }
    public function match(string $key, $values) : bool
    {
        return isset($values[self::PATH]);
    }
    public function convertToMethodCall(string $key, $values) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression
    {
        $variable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::ROUTING_CONFIGURATOR);
        // @todo args
        $args = $this->createAddArgs($key, $values);
        $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($variable, 'add', $args);
        foreach (self::NESTED_KEYS as $nestedKey) {
            if (!isset($values[$nestedKey])) {
                continue;
            }
            $nestedValues = $values[$nestedKey];
            // Transform methods as string GET|HEAD to array
            if ($nestedKey === self::METHODS && \is_string($nestedValues)) {
                $nestedValues = \explode('|', $nestedValues);
            }
            $args = $this->argsNodeFactory->createFromValues([$nestedValues]);
            $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, $nestedKey, $args);
        }
        return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($methodCall);
    }
    /**
     * @param mixed $values
     * @return Arg[]
     */
    private function createAddArgs(string $key, $values) : array
    {
        $argumentValues = [];
        $argumentValues[] = $key;
        if (isset($values[self::PATH])) {
            $argumentValues[] = $values[self::PATH];
        }
        return $this->argsNodeFactory->createFromValues($argumentValues);
    }
}
