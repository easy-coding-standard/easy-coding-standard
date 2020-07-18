<?php declare(strict_types=1);

use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyCodingStandard\FixerRunner\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception/*']);

    $services->set(UnifiedDiffer::class);

    $services->alias(DifferInterface::class, UnifiedDiffer::class);

    $services->set(FixerFileProcessor::class);
};
