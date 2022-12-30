<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(VisibilityRequiredFixer::class, [
        'elements' => ['const', 'property', 'method'],
    ]);

    $ecsConfig->rule(SingleBlankLineAtEofFixer::class);
};
