<?php

declare (strict_types=1);
namespace ECSPrefix202606;

use Symplify\CodingStandard\Fixer\Commenting\AddMissingParamNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\AddMissingVarNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\DoubleAsteriskInlineVarFixer;
use Symplify\CodingStandard\Fixer\Commenting\FixParamNameTypoFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveDeadParamFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveParamNameReferenceFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousReturnNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousVarNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\SingleLineInlineVarDocBlockFixer;
use Symplify\CodingStandard\Fixer\Commenting\SwitchedTypeAndNameFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rules([
        // inline @var
        DoubleAsteriskInlineVarFixer::class,
        SingleLineInlineVarDocBlockFixer::class,
        AddMissingVarNameFixer::class,
        // @param
        AddMissingParamNameFixer::class,
        FixParamNameTypoFixer::class,
        RemoveParamNameReferenceFixer::class,
        RemoveDeadParamFixer::class,
        // superfluous names
        RemoveSuperfluousReturnNameFixer::class,
        RemoveSuperfluousVarNameFixer::class,
        // switched type/name order
        SwitchedTypeAndNameFixer::class,
    ]);
};
