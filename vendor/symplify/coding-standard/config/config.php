<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\CodingStandard\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/TokenRunner/ValueObject', __DIR__ . '/../src/TokenRunner/Exception', __DIR__ . '/../src/Fixer', __DIR__ . '/../src/ValueObject']);
    $services->set(\PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer::class);
    $services->set(\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer::class);
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
    $services->set(\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector::class);
};
