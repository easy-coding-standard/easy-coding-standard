<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;

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

function isDebug(): bool
{
    $argvInput = new ArgvInput();
    return (bool) $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
}

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

// Build DI container
$easyCodingStandardKernel = new EasyCodingStandardKernel('prod' . computeConfigHash($configs), isDebug());
if ($configs !== []) {
    $easyCodingStandardKernel->setConfigs($configs);
}

$easyCodingStandardKernel->boot();

return $easyCodingStandardKernel->getContainer();
