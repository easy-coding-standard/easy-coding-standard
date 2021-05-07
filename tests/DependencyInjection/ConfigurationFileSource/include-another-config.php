<?php

declare (strict_types=1);
namespace ECSPrefix20210507;

use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/simple-config.php');
    $services = $containerConfigurator->services();
    $services->set(ClassDefinitionFixer::class);
};
