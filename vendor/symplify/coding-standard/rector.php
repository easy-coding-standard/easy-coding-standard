<?php

declare (strict_types=1);
namespace ECSPrefix202510;

use ECSPrefix202510\Rector\Config\RectorConfig;
return RectorConfig::configure()->withPaths([__DIR__ . '/config', __DIR__ . '/src', __DIR__ . '/tests'])->withRootFiles()->withPhpSets()->withPreparedSets(\false, \true, \true, \false, \false, \true, \true, \false, \true)->withImportNames()->withSkip(['*/Source/*', '*/Fixture/*']);
