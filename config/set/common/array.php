<?php

declare (strict_types=1);
namespace ECSPrefix20220429;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rule(\PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer::class);
    $ecsConfig->rule(\Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer::class);
    $ecsConfig->rule(\Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer::class);
    $ecsConfig->rule(\Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer::class);
    // commas
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::class, ['elements' => [\PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS]]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, ['syntax' => 'short']);
};
