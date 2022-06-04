<?php

declare (strict_types=1);
namespace ECSPrefix20220604;

use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([\PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer::class, \PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer::class, \PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer::class, \Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer::class]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class, ['remove_inheritdoc' => \true, 'allow_mixed' => \true]);
};
