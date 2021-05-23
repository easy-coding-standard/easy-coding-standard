<?php

declare (strict_types=1);
namespace ECSPrefix20210523;

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use ECSPrefix20210523\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210523\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->set(\PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff::class);
};
