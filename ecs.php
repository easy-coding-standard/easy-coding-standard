<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/bin', __DIR__ . '/config', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withEditorConfig()
    ->withRootFiles()
    ->withSkip(['*/Source/*', '*/Fixture/*'])
    ->withPreparedSets(symplify: true, psr12: true, common: true);
