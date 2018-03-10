<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;
use Symplify\PackageBuilder\Configuration\LevelConfigShortcutFinder;

require_once __DIR__ . '/easy-coding-standard-bootstrap.php';

// 1. Detect configuration from --level
$configFile = (new LevelConfigShortcutFinder())->resolveLevel(new ArgvInput(), __DIR__ . '/../config/');

// 2. Detect configuration
if ($configFile === null) {
    ConfigFilePathHelper::detectFromInput('ecs', new ArgvInput());
    $configFile = ConfigFilePathHelper::provide('ecs', 'easy-coding-standard.yml');
} else {
    ConfigFilePathHelper::set('ecs', $configFile);
}

// 3. Build DI container
$containerFactory = new ContainerFactory();
if ($configFile) {
    return $containerFactory->createWithConfig($configFile);
}

return $containerFactory->create();
