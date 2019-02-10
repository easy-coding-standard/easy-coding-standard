<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;
use Symplify\PackageBuilder\Console\Input\InputDetector;

// Detect configuration from level option
$configs = [];
$configs[] = (new LevelFileFinder())->detectFromInputAndDirectory(new ArgvInput(), __DIR__ . '/../config/');

// Fallback to config option
ConfigFileFinder::detectFromInput('ecs', new ArgvInput());
$configs[] = ConfigFileFinder::provide(
    'ecs',
    ['easy-coding-standard.yml', 'easy-coding-standard.yaml', 'ecs.yml', 'ecs.yaml']
);

// remove empty values
$configs = array_filter($configs);

/**
 * @param string[] $configs
 */
function computeConfigHash(array $configs): string
{
    $hash = '';
    foreach ($configs as $config) {
        $hash .= md5_file($config);
    }

    return $hash;
}

$environment = 'prod' . computeConfigHash($configs);
$easyCodingStandardKernel = new EasyCodingStandardKernel($environment, InputDetector::isDebug());

if ($configs !== []) {
    $easyCodingStandardKernel->setConfigs($configs);
}

$easyCodingStandardKernel->boot();

return $easyCodingStandardKernel->getContainer();
