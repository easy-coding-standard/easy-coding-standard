<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\FifthElement;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\SixthSense;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        // windows like path
        '*\SomeSkipped\*',

        // elements
        FifthElement::class,
        SixthSense::class,
    ]);
};
