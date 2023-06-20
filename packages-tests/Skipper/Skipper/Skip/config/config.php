<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\AnotherClassToSkip;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\SomeClassToSkip;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->skip([
        // classes
        SomeClassToSkip::class,

        AnotherClassToSkip::class => ['Fixture/someFile', '*/someDirectory/*'],

        // code
        AnotherClassToSkip::class . '.someCode' => null,
        AnotherClassToSkip::class . '.someOtherCode' => ['*/someDirectory/*'],
        AnotherClassToSkip::class . '.someAnotherCode' => ['someDirectory/*'],

        // file paths
        __DIR__ . '/../Fixture/AlwaysSkippedPath',
        '*\PathSkippedWithMask\*',

        // messages
        'some fishy code at line 5!' => null,
        'some another fishy code at line 5!' => ['someDirectory/*'],
        'Cognitive complexity for method "foo" is 2 but has to be less than or equal to 1.' => null,
    ]);
};
