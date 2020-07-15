<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');

    $containerConfigurator->import(__DIR__ . '/parameters/parameter_name_guard.php');

    $containerConfigurator->import(__DIR__ . '/../packages/*/config/*.php');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('indentation', 'spaces');

    $parameters->set('line_ending', PHP_EOL);

    $parameters->set('cache_directory', sys_get_temp_dir() . '/_changed_files_detector%env(TEST_SUFFIX)%');

    $parameters->set('cache_namespace', Strings::webalize(getcwd()));

    $parameters->set(Option::SKIP, []);

    $parameters->set(Option::ONLY, []);

    $parameters->set(Option::PATHS, []);

    $parameters->set(Option::SETS, []);

    $parameters->set('file_extensions', ['php']);

    $parameters->set(Option::EXCLUDE_PATHS, []);

    $parameters->set(Option::EXCLUDE_FILES, []);

    $parameters->set('env(TEST_SUFFIX)', '');
};
