<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->import(__DIR__ . '/config.php');
    $services = $ecsConfig->services();
    $services->defaults()->public()->autowire();
    $services->set(FinalInternalClassFixer::class);
    $services->load('Symplify\\CodingStandard\\Fixer\\', __DIR__ . '/../src/Fixer')->exclude([__DIR__ . '/../src/Fixer/Annotation', __DIR__ . '/../src/Fixer/Spacing/StandaloneLineConstructorParamFixer.php']);
    $services->set(GeneralPhpdocAnnotationRemoveFixer::class)->call('configure', [['annotations' => ['throws', 'author', 'package', 'group', 'covers']]]);
};
