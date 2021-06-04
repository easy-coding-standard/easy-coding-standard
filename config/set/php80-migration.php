<?php

declare (strict_types=1);
namespace ECSPrefix20210604;

use PhpCsFixer\Fixer\Alias\BacktickToShellExecFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Whitespace\HeredocIndentationFixer;
use ECSPrefix20210604\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210604\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    \trigger_error('ECS set PHP_80_MIGRATION is deprecated. Use more advanced and precise Rector instead (http://github.com/rectorphp/rector)');
    \sleep(3);
    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\Alias\BacktickToShellExecFixer::class);
    $services->set(\PhpCsFixer\Fixer\Whitespace\HeredocIndentationFixer::class);
    $services->set(\PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer::class);
    $services->set(\PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer::class);
    $services->set(\PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer::class);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class)->call('configure', [['elements' => ['const', 'method', 'property']]]);
};
