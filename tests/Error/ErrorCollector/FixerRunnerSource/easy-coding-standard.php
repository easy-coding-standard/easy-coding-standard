<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(VisibilityRequiredFixer::class)
        ->call('configure', [[
            'elements' => ['const', 'property', 'method'],
        ]]);

    $services->set(SingleBlankLineAtEofFixer::class);
};
