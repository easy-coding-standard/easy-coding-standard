<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    trigger_error(
        'ECS set PHPUNIT_32_MIGRATION_RISKY is deprecated. Use more advanced and precise Rector instead (http://github.com/rectorphp/rector)'
    );
    sleep(3);

    $services = $containerConfigurator->services();
    $services->set(PhpUnitDedicateAssertFixer::class)
        ->call('configure', [[
            'target' => '3.0',
        ]]);
    $services->set(PhpUnitNoExpectationAnnotationFixer::class)
        ->call('configure', [[
            'target' => '3.2',
        ]]);
};
