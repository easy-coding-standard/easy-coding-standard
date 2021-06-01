<?php

declare (strict_types=1);
namespace ConfigTransformer20210601;

use ConfigTransformer20210601\PhpParser\BuilderFactory;
use ConfigTransformer20210601\PhpParser\NodeFinder;
use ConfigTransformer20210601\Symfony\Component\Console\Application;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ConfigTransformer20210601\Symfony\Component\Yaml\Parser;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Console\ConfigTransfomerConsoleApplication;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Provider\YamlContentProvider;
use ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command\CommandNaming;
use ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemFilter;
return static function (\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ConfigTransformer20210601\Symplify\ConfigTransformer\\', __DIR__ . '/../src')->exclude([
        __DIR__ . '/../src/HttpKernel',
        __DIR__ . '/../src/DependencyInjection/Loader',
        __DIR__ . '/../src/ValueObject',
        // configurable class for faking extensions
        __DIR__ . '/../src/DependencyInjection/Extension/AliasConfigurableExtension.php',
    ]);
    // console
    $services->set(\ConfigTransformer20210601\Symplify\ConfigTransformer\Console\ConfigTransfomerConsoleApplication::class);
    $services->alias(\ConfigTransformer20210601\Symfony\Component\Console\Application::class, \ConfigTransformer20210601\Symplify\ConfigTransformer\Console\ConfigTransfomerConsoleApplication::class);
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command\CommandNaming::class);
    $services->set(\ConfigTransformer20210601\PhpParser\BuilderFactory::class);
    $services->set(\ConfigTransformer20210601\PhpParser\NodeFinder::class);
    $services->set(\ConfigTransformer20210601\Symfony\Component\Yaml\Parser::class);
    $services->set(\ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemFilter::class);
    $services->alias(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface::class, \ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration::class);
    $services->alias(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface::class, \ConfigTransformer20210601\Symplify\ConfigTransformer\Provider\YamlContentProvider::class);
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
};
