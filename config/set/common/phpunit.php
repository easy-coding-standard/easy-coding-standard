<?php

declare (strict_types=1);
namespace ECSPrefix20220607;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([PhpUnitTestAnnotationFixer::class, PhpUnitSetUpTearDownVisibilityFixer::class]);
};
