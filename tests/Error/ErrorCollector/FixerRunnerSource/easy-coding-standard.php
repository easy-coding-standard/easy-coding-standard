<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withConfiguredRule(VisibilityRequiredFixer::class, [
        'elements' => ['const', 'property', 'method'],
    ])
    ->withRules([SingleBlankLineAtEofFixer::class]);
