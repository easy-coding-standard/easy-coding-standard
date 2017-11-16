<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Application;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;
use Symplify\PackageBuilder\Configuration\LevelConfigShortcutFinder;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

// performance boost
gc_disable();

$possibleAutoloadPaths = [
    // composer create-project
    __DIR__ . '/../../..',
    // composer require
    __DIR__ . '/../vendor',
    // mono-repository
    __DIR__ . '/../../../vendor',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (is_file($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        require_once $possibleAutoloadPath . '/squizlabs/php_codesniffer/autoload.php';

        break;
    }
}

try {
    // 1. Detect configuration from --level
    $configFile = (new LevelConfigShortcutFinder())->resolveLevel(new ArgvInput(), __DIR__ . '/../config/');

    // 2. Detect configuration
    if ($configFile === null) {
        ConfigFilePathHelper::detectFromInput('ecs', new ArgvInput());
        $configFile = ConfigFilePathHelper::provide('ecs', 'easy-coding-standard.neon');
    }

    // 3. Build DI container
    $containerFactory = new ContainerFactory();
    if ($configFile) {
        $container = $containerFactory->createWithConfig($configFile);
    } else {
        $container = $containerFactory->create();
    }

    // 4. Run Console Application
    /** @var Application $application */
    $application = $container->get(Application::class);
    /** @var InputInterface $input */
    $input = $container->get(InputInterface::class);
    /** @var OutputInterface $output */
    $output = $container->get(OutputInterface::class);
    exit($application->run($input, $output));
} catch (Throwable $throwable) {
    $symfonyStyle = SymfonyStyleFactory::create();
    $symfonyStyle->error($throwable->getMessage());
    exit(1);
}
