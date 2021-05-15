<?php

namespace ECSPrefix20210515;

use ECSPrefix20210515\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210515\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix20210515\Symplify\Skipper\ValueObject\Option;
use ECSPrefix20210515\Symplify\SmartFileSystem\Normalizer\PathNormalizer;
return static function (\ECSPrefix20210515\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\ECSPrefix20210515\Symplify\Skipper\ValueObject\Option::SKIP, []);
    $parameters->set(\ECSPrefix20210515\Symplify\Skipper\ValueObject\Option::ONLY, []);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210515\Symplify\Skipper\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle', __DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);
    $services->set(\ECSPrefix20210515\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
    $services->set(\ECSPrefix20210515\Symplify\SmartFileSystem\Normalizer\PathNormalizer::class);
};
