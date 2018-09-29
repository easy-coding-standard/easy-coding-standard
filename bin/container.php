<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;

// Detect configuration from level option
$configFiles = [];
$configFiles[] = (new LevelFileFinder())->detectFromInputAndDirectory(new ArgvInput(), __DIR__ . '/../config/');

// Fallback to config option
ConfigFileFinder::detectFromInput('ecs', new ArgvInput());
$configFiles[] = ConfigFileFinder::provide(
    'ecs',
    ['easy-coding-standard.yml', 'easy-coding-standard.yaml', 'ecs.yml', 'ecs.yaml']
);

// remove empty values
$configFiles = array_filter($configFiles);

// Build DI container
$containerFactory = new ContainerFactory();
if ($configFiles) {
    return $containerFactory->createWithConfigs($configFiles);
}

return $containerFactory->create();
