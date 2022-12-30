<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(IndentationTypeFixer::class);
    $ecsConfig->rule(DisallowTabIndentSniff::class);

    // See https://github.com/symplify/symplify/issues/1702
    $ecsConfig->rule(IncludeFixer::class);
    $ecsConfig->rule(LanguageConstructSpacingSniff::class);
};
