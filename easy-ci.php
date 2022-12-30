<?php

declare(strict_types=1);

use Symplify\EasyCI\Config\EasyCIConfig;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->excludeCheckPaths(['Fixture', 'Source', 'tests', 'stubs', 'templates']);

    $easyCIConfig->typesToSkip([
        // factory in config
        \Symplify\EasyCodingStandard\Caching\CacheFactory::class,
        \Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory::class,
        \Symplify\EasyCodingStandard\Skipper\Contract\SkipVoterInterface::class,
        \Symfony\Component\Console\Application::class,
        \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory::class,
        \Symplify\EasyCodingStandard\DependencyInjection\EasyCodingStandardContainerFactory::class,
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::class,
   ]);
};
