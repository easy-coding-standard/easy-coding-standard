<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        DeclareStrictTypesFixer::class => ['Source/someFile', '*/someDirectory/*'],

        # code
        DeclareStrictTypesFixer::class . '.someCode' => null,
        DeclareStrictTypesFixer::class . '.someOtherCode' => ['*/someDirectory/*'],
        DeclareStrictTypesFixer::class . '.someAnotherCode' => ['someDirectory/*'],
        # messages
        'some fishy code at line 5!' => null,
        'some another fishy code at line 5!' => ['someDirectory/*'],
        'Cognitive complexity for method "foo" is 2 but has to be less than or equal to 1.' => null,
    ]);

    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class);
};
