<?php
declare(strict_types=1);

use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(PhpUnitDedicateAssertFixer::class)
        ->call('configure', [[
            'target' => '5.6',
        ]]);
    $services->set(PhpUnitExpectationFixer::class)
        ->call('configure', [[
            'target' => '5.6',
        ]]);
    $services->set(PhpUnitMockFixer::class)
        ->call('configure', [[
            'target' => '5.5',
        ]]);
    $services->set(PhpUnitNamespacedFixer::class)
        ->call('configure', [[
            'target' => '6.0',
        ]]);
    $services->set(PhpUnitNoExpectationAnnotationFixer::class)
        ->call('configure', [[
            'target' => '4.3',
        ]]);
};
