<?php

declare (strict_types=1);
namespace ECSPrefix202606;

use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemoveMethodNameDuplicateDescriptionFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemovePHPStormAnnotationFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemovePropertyVariableNameDescriptionFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLineConstructorParamFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rules([
        // docblocks and comments
        RemovePHPStormAnnotationFixer::class,
        RemoveUselessDefaultCommentFixer::class,
        RemoveMethodNameDuplicateDescriptionFixer::class,
        RemovePropertyVariableNameDescriptionFixer::class,
        // arrays
        ArrayListItemNewlineFixer::class,
        ArrayOpenerAndCloserNewlineFixer::class,
        StandaloneLineInMultilineArrayFixer::class,
        StandaloneLinePromotedPropertyFixer::class,
        StandaloneLineConstructorParamFixer::class,
        // newlines
        MethodChainingNewlineFixer::class,
        SpaceAfterCommaHereNowDocFixer::class,
        BlankLineAfterStrictTypesFixer::class,
        // line length
        LineLengthFixer::class,
    ]);
    $ecsConfig->ruleWithConfiguration(GeneralPhpdocAnnotationRemoveFixer::class, ['annotations' => ['throws', 'author', 'package', 'group', 'covers', 'category']]);
};
