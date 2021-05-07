<?php

namespace ECSPrefix20210507;

use ECSPrefix20210507\Rector\Core\Configuration\Option;
use ECSPrefix20210507\Rector\Set\ValueObject\DowngradeSetList;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(DowngradeSetList::PHP_80);
    $containerConfigurator->import(DowngradeSetList::PHP_74);
    $containerConfigurator->import(DowngradeSetList::PHP_73);
    $containerConfigurator->import(DowngradeSetList::PHP_72);
    $containerConfigurator->import(DowngradeSetList::PHP_71);
    $containerConfigurator->import(DowngradeSetList::PHP_70);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, ['*/Tests/*', '*/tests/*', __DIR__ . '/../../tests']);
};
