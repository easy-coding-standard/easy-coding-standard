<?php

declare (strict_types=1);
namespace ECSPrefix202212;

use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\CodingStandard\Fixer\Spacing\NewlineServiceDefinitionConfigFixer;
use Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->import(__DIR__ . '/config.php');
    $services = $ecsConfig->services();
    $services->defaults()->public()->autowire();
    $services->set(FinalInternalClassFixer::class);
    $services->load('Symplify\\CodingStandard\\Fixer\\', __DIR__ . '/../src/Fixer')->exclude([__DIR__ . '/../src/Fixer/Spacing', __DIR__ . '/../src/Fixer/Annotation']);
    // include rules from spacing, except only promoted property
    // the file exclude does not work since Symfony 6.2 for some reason, so it must be done this way
    $services->set(MethodChainingNewlineFixer::class);
    $services->set(NewlineServiceDefinitionConfigFixer::class);
    $services->set(SpaceAfterCommaHereNowDocFixer::class);
    $services->set(StandaloneLinePromotedPropertyFixer::class);
    $services->set(GeneralPhpdocAnnotationRemoveFixer::class)->call('configure', [['annotations' => ['throws', 'author', 'package', 'group', 'covers']]]);
};
