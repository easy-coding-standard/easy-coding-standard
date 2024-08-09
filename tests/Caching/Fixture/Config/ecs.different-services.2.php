<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withParallel(timeoutSeconds: 120, maxNumberOfProcess: 32, jobSize: 20);;
