<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\PhpParser\NodeFactory;

use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\Node\Expr\Closure;
use ConfigTransformer20210601\PhpParser\Node\Expr\Variable;
use ConfigTransformer20210601\PhpParser\Node\Identifier;
use ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified;
use ConfigTransformer20210601\PhpParser\Node\Param;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ConfigTransformer20210601\Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName;
final class ConfiguratorClosureNodeFactory
{
    /**
     * @param Node[] $stmts
     */
    public function createContainerClosureFromStmts(array $stmts) : \ConfigTransformer20210601\PhpParser\Node\Expr\Closure
    {
        $param = $this->createContainerConfiguratorParam();
        return $this->createClosureFromParamAndStmts($param, $stmts);
    }
    /**
     * @param Node[] $stmts
     */
    public function createRoutingClosureFromStmts(array $stmts) : \ConfigTransformer20210601\PhpParser\Node\Expr\Closure
    {
        $param = $this->createRoutingConfiguratorParam();
        return $this->createClosureFromParamAndStmts($param, $stmts);
    }
    private function createContainerConfiguratorParam() : \ConfigTransformer20210601\PhpParser\Node\Param
    {
        $containerConfiguratorVariable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::CONTAINER_CONFIGURATOR);
        return new \ConfigTransformer20210601\PhpParser\Node\Param($containerConfiguratorVariable, null, new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator::class));
    }
    private function createRoutingConfiguratorParam() : \ConfigTransformer20210601\PhpParser\Node\Param
    {
        $containerConfiguratorVariable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::ROUTING_CONFIGURATOR);
        return new \ConfigTransformer20210601\PhpParser\Node\Param($containerConfiguratorVariable, null, new \ConfigTransformer20210601\PhpParser\Node\Name\FullyQualified(\ConfigTransformer20210601\Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator::class));
    }
    private function createClosureFromParamAndStmts(\ConfigTransformer20210601\PhpParser\Node\Param $param, array $stmts) : \ConfigTransformer20210601\PhpParser\Node\Expr\Closure
    {
        $closure = new \ConfigTransformer20210601\PhpParser\Node\Expr\Closure(['params' => [$param], 'stmts' => $stmts, 'static' => \true]);
        // is PHP 7.1? → add "void" return type
        if (\version_compare(\PHP_VERSION, '7.1.0') >= 0) {
            $closure->returnType = new \ConfigTransformer20210601\PhpParser\Node\Identifier('void');
        }
        return $closure;
    }
}
