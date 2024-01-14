<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\FifthElement;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\SixthSense;

return ECSConfig::configure()
    ->withSkip([
        // windows like path
        '*\SomeSkipped\*',

        // elements
        FifthElement::class,
        SixthSense::class,
    ]);
