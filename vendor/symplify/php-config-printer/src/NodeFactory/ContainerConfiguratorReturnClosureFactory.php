<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory;

use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\Node\Expr\Assign;
use ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
use ConfigTransformer20210601\PhpParser\Node\Expr\Variable;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Expression;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Return_;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\PhpParser\NodeFactory\ConfiguratorClosureNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey;
final class ContainerConfiguratorReturnClosureFactory
{
    /**
     * @var ConfiguratorClosureNodeFactory
     */
    private $configuratorClosureNodeFactory;
    /**
     * @var CaseConverterInterface[]
     */
    private $caseConverters = [];
    /**
     * @var ContainerNestedNodesFactory
     */
    private $containerNestedNodesFactory;
    /**
     * @param CaseConverterInterface[] $caseConverters
     */
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\PhpParser\NodeFactory\ConfiguratorClosureNodeFactory $configuratorClosureNodeFactory, array $caseConverters, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ContainerNestedNodesFactory $containerNestedNodesFactory)
    {
        $this->configuratorClosureNodeFactory = $configuratorClosureNodeFactory;
        $this->caseConverters = $caseConverters;
        $this->containerNestedNodesFactory = $containerNestedNodesFactory;
    }
    public function createFromYamlArray(array $arrayData) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Return_
    {
        $stmts = $this->createClosureStmts($arrayData);
        $closure = $this->configuratorClosureNodeFactory->createContainerClosureFromStmts($stmts);
        return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Return_($closure);
    }
    /**
     * @return Node[]
     */
    private function createClosureStmts(array $yamlData) : array
    {
        $yamlData = \array_filter($yamlData);
        return $this->createNodesFromCaseConverters($yamlData);
    }
    /**
     * @param mixed[] $yamlData
     * @return Node[]
     */
    private function createNodesFromCaseConverters(array $yamlData) : array
    {
        $nodes = [];
        foreach ($yamlData as $key => $values) {
            $nodes = $this->createInitializeNode($key, $nodes);
            foreach ($values as $nestedKey => $nestedValues) {
                $nestedNodes = [];
                if (\is_array($nestedValues)) {
                    $nestedNodes = $this->containerNestedNodesFactory->createFromValues($nestedValues, $key, $nestedKey);
                }
                if ($nestedNodes !== []) {
                    $nodes = \array_merge($nodes, $nestedNodes);
                    continue;
                }
                $expression = $this->resolveExpression($key, $nestedKey, $nestedValues);
                if (!$expression instanceof \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression) {
                    continue;
                }
                $nodes[] = $expression;
            }
        }
        return $nodes;
    }
    private function createInitializeAssign(string $variableName, string $methodName) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression
    {
        $servicesVariable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable($variableName);
        $containerConfiguratorVariable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::CONTAINER_CONFIGURATOR);
        $assign = new \ConfigTransformer20210601\PhpParser\Node\Expr\Assign($servicesVariable, new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($containerConfiguratorVariable, $methodName));
        return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($assign);
    }
    /**
     * @return mixed[]
     */
    private function createInitializeNode(string $key, array $nodes) : array
    {
        if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::SERVICES) {
            $nodes[] = $this->createInitializeAssign(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::SERVICES, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::SERVICES);
        }
        if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::PARAMETERS) {
            $nodes[] = $this->createInitializeAssign(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::PARAMETERS, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\MethodName::PARAMETERS);
        }
        return $nodes;
    }
    /**
     * @param int|string $nestedKey
     * @param mixed|mixed[] $nestedValues
     * @return \PhpParser\Node\Stmt\Expression|null
     */
    private function resolveExpression(string $key, $nestedKey, $nestedValues)
    {
        foreach ($this->caseConverters as $caseConverter) {
            if (!$caseConverter->match($key, $nestedKey, $nestedValues)) {
                continue;
            }
            /** @var string $nestedKey */
            return $caseConverter->convertToMethodCall($nestedKey, $nestedValues);
        }
        return null;
    }
}
