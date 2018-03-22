<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;

require_once __DIR__ . '/easy-coding-standard-bootstrap.php';

// 1. Detect configuration from --level
$configFile = (new LevelFileFinder())->detectFromInputAndDirectory(new ArgvInput(), __DIR__ . '/../config/');

// 2. Detect configuration
if ($configFile === null) {
    ConfigFileFinder::detectFromInput('ecs', new ArgvInput());
    $configFile = ConfigFileFinder::provide('ecs', ['easy-coding-standard.yml', 'easy-coding-standard.yaml']);
} else {
    ConfigFileFinder::set('ecs', $configFile);
}

// 3. Build DI container
$containerFactory = new ContainerFactory();
if ($configFile) {
    return $containerFactory->createWithConfig($configFile);
}

return $containerFactory->create();
