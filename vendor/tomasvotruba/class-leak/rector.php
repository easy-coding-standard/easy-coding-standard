<?php

declare (strict_types=1);
namespace ECSPrefix202606;

use ECSPrefix202606\Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use ECSPrefix202606\Rector\Config\RectorConfig;
return RectorConfig::configure()->withPaths([__DIR__ . '/bin', __DIR__ . '/src', __DIR__ . '/tests'])->withPreparedSets(\true, \true, \true, \true, \true, \true, \true, \false, \false, \true, \false, \false, \false, \true)->withPhpSets()->withRootFiles()->withImportNames()->withSkip(['*/scoper.php', '*/Source/*', '*/Fixture/*', StringClassNameToClassConstantRector::class => [__DIR__ . '/src/Filtering/PossiblyUnusedClassesFilter.php']]);
