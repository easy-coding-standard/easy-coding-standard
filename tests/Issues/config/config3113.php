<?php

declare (strict_types=1);
namespace ECSPrefix20210507;

use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(SetList::ARRAY);
};
