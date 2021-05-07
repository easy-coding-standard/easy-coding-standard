<?php

namespace ECSPrefix20210507;

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->set(NoUnusedImportsFixer::class);
    $services->set(OrderedImportsFixer::class);
    $services->set(SingleBlankLineBeforeNamespaceFixer::class);
};
