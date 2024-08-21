<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withParallel(timeoutSeconds: 15, maxNumberOfProcess: 16, jobSize: 10);;
