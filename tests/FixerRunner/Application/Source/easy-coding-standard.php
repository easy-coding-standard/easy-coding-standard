<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayDeclarationSniff;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([
        // priority 0 - lower last
        NoTrailingCommaInSinglelineArrayFixer::class,

        ArrayDeclarationSniff::class,

        // priority 100 - higher first
        EncodingFixer::class,

        // priority 98
        FullOpeningTagFixer::class,
    ]);
