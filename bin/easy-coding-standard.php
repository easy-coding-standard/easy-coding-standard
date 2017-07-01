<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;

// performance boost
gc_disable();

/**
 * This allows to load "vendor/autoload.php" both from
 * "composer create-project ..." and "composer require" installation.
 */
$possibleAutoloadPaths = [__DIR__ . '/../../..', __DIR__ . '/../vendor', __DIR__ . '/../../../vendor'];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        require_once $possibleAutoloadPath . '/squizlabs/php_codesniffer/autoload.php';
        break;
    }
}

// 1. Detect configuration
ConfigFilePathHelper::detectFromInput('ecs', new ArgvInput);

// 2. Build DI container
$containerFactory = new ContainerFactory;
$configFile = ConfigFilePathHelper::provide('ecs', 'easy-coding-standard.neon');
if ($configFile) {
    $container = $containerFactory->createWithConfig($configFile);
} else {
    $container = $containerFactory->create();
}

// 3. Run Console Application
/** @var Application $application */
$application = $container->get(Application::class);
$application->run();
