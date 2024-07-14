<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withConfiguredRule(FinalInternalClassFixer::class, [
        'annotation_exclude' => ['@not-fix'],
        'consider_absent_docblock_as_internal_class' => \true
    ]);
