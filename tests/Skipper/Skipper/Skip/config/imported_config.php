<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\AnotherClassToSkip;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\SomeClassToSkip;

return static function (ECSConfig $config): void {
    $config->skip([
        SomeClassToSkip::class => ['Fixture/someDirectory/someFile.php'],
        AnotherClassToSkip::class => null,
    ]);
};
