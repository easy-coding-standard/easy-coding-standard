<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Tests\SniffRunner\DI\Source\AnotherSniff;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(AnotherSniff::class, [
        'lineLimit' => 15,
        'absoluteLineLimit' => [
            // just test array of annotations
            '@author',
        ],
    ]);
};
