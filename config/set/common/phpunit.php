<?php

declare (strict_types=1);
namespace ECSPrefix202306;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([PhpUnitTestAnnotationFixer::class, PhpUnitSetUpTearDownVisibilityFixer::class]);
};
