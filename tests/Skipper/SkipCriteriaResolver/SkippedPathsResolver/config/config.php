<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSkip([
        // windows slashes
        __DIR__ . '\non-existing-path',
        __DIR__ . '/../Fixture',
        '*\Mask\*',
    ]);
