<?php

declare(strict_types = 1);

use PhpCsFixer\Fixer\Alias\PowToExponentiationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(PowToExponentiationFixer::class);
};
