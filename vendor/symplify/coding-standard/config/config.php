<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use ECSPrefix202208\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ECSConfig $ecsConfig) : void {
    $services = $ecsConfig->services();
    $services->defaults()->public()->autowire();
    $services->load('Symplify\\CodingStandard\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/TokenRunner/ValueObject', __DIR__ . '/../src/TokenRunner/Exception', __DIR__ . '/../src/Fixer', __DIR__ . '/../src/Exception', __DIR__ . '/../src/ValueObject']);
    $services->set(NamespaceUsesAnalyzer::class);
    $services->set(FunctionsAnalyzer::class);
    $services->set(PrivatesAccessor::class);
    $services->set(ChangedFilesDetector::class);
};
