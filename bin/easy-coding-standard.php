<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

// performance boost
gc_disable();

// 0. Prefer local vendor over analyzed project (e.g. for "composer create-project symplify/easy-coding-standard")
$possibleAutoloadPaths = [
    __DIR__ . '/../../..',
    __DIR__ . '/../vendor',
    getcwd() . '/vendor',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        require_once $possibleAutoloadPath . '/squizlabs/php_codesniffer/autoload.php';
        break;
    }
}

// 1. Detect configuration
$input = new ArgvInput;
$configurationFile = null;
if ($input->hasParameterOption('--configuration') || $input->hasParameterOption('-c')) {
    $filePath = getcwd() . '/' . $input->getParameterOption('-c');
    if (file_exists($filePath)) {
        $configurationFile = $filePath;
    }

    $filePath = getcwd() . '/' . $input->getParameterOption('--configuration');
    if (file_exists($filePath)) {
        $configurationFile = $filePath;
    }
}

// 2. Build DI container
if ($configurationFile) {
    $container = (new ContainerFactory)->createWithCustomConfig($configurationFile);
} else {
    $container = (new ContainerFactory)->create();
}

// 3. Run Console Application
/** @var Application $application */
$application = $container->get(Application::class);
$application->run();
