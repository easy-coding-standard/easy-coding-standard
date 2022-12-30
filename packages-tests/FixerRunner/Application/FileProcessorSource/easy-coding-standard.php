<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayDeclarationSniff;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    // priority 0 - lower last
    $ecsConfig->rule(NoTrailingCommaInSinglelineArrayFixer::class);

    $ecsConfig->rule(ArrayDeclarationSniff::class);

    // priority 100 - higher first
    $ecsConfig->rule(EncodingFixer::class);

    // priority 98
    $ecsConfig->rule(FullOpeningTagFixer::class);
};
