<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Tests\SniffRunner\DI\Source\AnotherSniff;

return ECSConfig::configure()
    ->withConfiguredRule(AnotherSniff::class, [
        'lineLimit' => 15,
        'absoluteLineLimit' => 55,
    ]);
