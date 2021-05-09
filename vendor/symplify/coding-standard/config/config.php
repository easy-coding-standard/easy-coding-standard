<?php

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\CodingStandard\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Bundle',
            __DIR__ . '/../src/TokenRunner/ValueObject',
            __DIR__ . '/../src/TokenRunner/Exception',
            __DIR__ . '/../src/Fixer',
            __DIR__ . '/../src/ValueObject',
        ]);

    $services->set(FunctionsAnalyzer::class);
    $services->set(PrivatesAccessor::class);

    $services->set(ChangedFilesDetector::class);
};
