<?php

namespace ECSPrefix20210513;

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use ECSPrefix20210513\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210513\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->set(\PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff::class);
};
