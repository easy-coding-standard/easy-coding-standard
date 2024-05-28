<?php

declare(strict_types=1);
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/config', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withRules([LineLengthFixer::class])
    ->withRootFiles()
    ->withSkip(['*/Source/*', '*/Fixture/*'])
    ->withPreparedSets(psr12: true, common: true);
