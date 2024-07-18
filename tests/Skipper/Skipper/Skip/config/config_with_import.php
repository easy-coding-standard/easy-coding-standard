<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\AnotherClassToSkip;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\SomeClassToSkip;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(__DIR__.'/imported_config.php');

    $ecsConfig->skip([
        SomeClassToSkip::class => 'Fixture/AlwaysSkippedPath',
        AnotherClassToSkip::class => null,
    ]);
};
