<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PhpUnitStrictFixer::class);

    $services->set(PhpUnitTestAnnotationFixer::class);

    $services->set(PhpUnitSetUpTearDownVisibilityFixer::class);
};
