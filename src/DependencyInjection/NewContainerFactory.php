<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Illuminate\Container\Container;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;

/**
 * @api will be used in new DI
 */
final class NewContainerFactory
{
    public function create(): Container
    {
        $container = new Container();

        $container->singleton(EasyCodingStandardStyle::class, static function (Container $container) {
            $easyCodingStandardStyleFactory = $container->make(EasyCodingStandardStyleFactory::class);
            return $easyCodingStandardStyleFactory->create();
        });

        return $container;
    }
}
