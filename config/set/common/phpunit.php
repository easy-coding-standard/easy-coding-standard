<?php

declare (strict_types=1);
namespace ECSPrefix20220605;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([\PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer::class, \PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer::class]);
};
