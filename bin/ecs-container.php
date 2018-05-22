<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;

// 1. Detect configuration from level option
$configFile = (new LevelFileFinder())->detectFromInputAndDirectory(new ArgvInput(), __DIR__ . '/../config/');

// 2. Fallback to config option
if ($configFile === null) {
    ConfigFileFinder::detectFromInput('ecs', new ArgvInput());
    // 3. Fallback to root file
    $configFile = ConfigFileFinder::provide(
        'ecs',
        ['easy-coding-standard.yml', 'easy-coding-standard.yaml', 'ecs.yml', 'ecs.yaml']
    );
} else {
    ConfigFileFinder::set('ecs', $configFile);
}

// 4. Build DI container
$containerFactory = new ContainerFactory();
if ($configFile) {
    return $containerFactory->createWithConfig($configFile);
}

return $containerFactory->create();
