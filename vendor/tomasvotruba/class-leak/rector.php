<?php

declare (strict_types=1);
namespace ECSPrefix202609;

use ECSPrefix202609\Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use ECSPrefix202609\Rector\Config\RectorConfig;
return RectorConfig::configure()->withPaths([__DIR__ . '/bin', __DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages'])->withPreparedSets(\true, \true, \true, \true, \true, \true, \true, \false, \false, \false, \true, \false, \false, \true)->withPhpSets()->withRootFiles()->withImportNames()->withSkip(['*/scoper.php', '*/Source/*', '*/Fixture/*', StringClassNameToClassConstantRector::class => [__DIR__ . '/src/Filtering/PossiblyUnusedClassesFilter.php']]);
