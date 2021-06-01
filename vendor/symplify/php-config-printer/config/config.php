<?php

declare (strict_types=1);
namespace ConfigTransformer20210601;

use ConfigTransformer20210601\PhpParser\BuilderFactory;
use ConfigTransformer20210601\PhpParser\NodeFinder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ConfigTransformer20210601\Symfony\Component\Yaml\Parser;
use ConfigTransformer20210601\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ConfigTransformer20210601\Symplify\PhpConfigPrinter\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/Dummy', __DIR__ . '/../src/Bundle']);
    $services->set(\ConfigTransformer20210601\PhpParser\NodeFinder::class);
    $services->set(\ConfigTransformer20210601\Symfony\Component\Yaml\Parser::class);
    $services->set(\ConfigTransformer20210601\PhpParser\BuilderFactory::class);
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerInterface::class)]);
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
};
