<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::ONLY, [
        LineLengthFixer::class => ['SomeFileToOnlyInclude.php'],
    ]);

    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class);
};
