<?php declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(UnusedPrivateElementsSniff::class);

    $services->set(UnusedInheritedVariablePassedToClosureSniff::class);

    $services->set(UnusedParameterSniff::class);

    $services->set(UnusedVariableSniff::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [UnusedParameterSniff::class . '.UnusedParameter' => null]);
};
