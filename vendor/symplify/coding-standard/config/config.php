<?php

declare (strict_types=1);
namespace ECSPrefix20210608;

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use ECSPrefix20210608\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\CodingStandard\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle', __DIR__ . '/../src/TokenRunner/ValueObject', __DIR__ . '/../src/TokenRunner/Exception', __DIR__ . '/../src/Fixer', __DIR__ . '/../src/ValueObject']);
    $services->set(\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer::class);
    $services->set(\ECSPrefix20210608\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
    $services->set(\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector::class);
};
