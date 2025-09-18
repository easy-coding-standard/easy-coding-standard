<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([
        IndentationTypeFixer::class,
        DisallowTabIndentSniff::class,

        // See https://github.com/symplify/symplify/issues/1702
        IncludeFixer::class,
        LanguageConstructSpacingSniff::class,
    ]);
