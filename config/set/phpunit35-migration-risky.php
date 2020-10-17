<?php
declare(strict_types=1);

use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(PhpUnitDedicateAssertFixer::class)
        ->call('configure', [[
            'target' => '3.5',
        ]]);
    $services->set(PhpUnitNoExpectationAnnotationFixer::class)
        ->call('configure', [[
            'target' => '3.2',
        ]]);
};
