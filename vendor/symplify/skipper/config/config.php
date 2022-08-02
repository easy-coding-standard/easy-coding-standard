<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use ECSPrefix202208\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202208\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix202208\Symplify\Skipper\ValueObject\Option;
use ECSPrefix202208\Symplify\SmartFileSystem\Normalizer\PathNormalizer;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, []);
    $parameters->set(Option::ONLY, []);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('ECSPrefix202208\Symplify\Skipper\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/ValueObject']);
    $services->set(ClassLikeExistenceChecker::class);
    $services->set(PathNormalizer::class);
};
