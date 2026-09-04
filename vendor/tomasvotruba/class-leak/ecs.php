<?php

declare (strict_types=1);
namespace ECSPrefix202609;

use Symplify\EasyCodingStandard\Config\ECSConfig;
return ECSConfig::configure()->withPaths([__DIR__ . '/bin', __DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages'])->withSkip([
    // invalid syntax test fixture
    __DIR__ . '/tests/UseImportsResolver/Fixture/ParseError.php',
    '*/Fixture/*',
    '*/Source/*',
])->withPreparedSets(\true, \true);
