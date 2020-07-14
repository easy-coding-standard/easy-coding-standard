<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/services/services_cache.php');

    $containerConfigurator->import(__DIR__ . '/../packages/*/config/*.php', 'glob');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('indentation', 'spaces');

    $parameters->set('line_ending', "\n");

    $parameters->set('cache_directory', '%sys_get_temp_dir%/_changed_files_detector%env(TEST_SUFFIX)%');

    $parameters->set('cache_namespace', '%getcwd_webalized%');

    $parameters->set('skip', []);

    $parameters->set('only', []);

    $parameters->set('paths', []);

    $parameters->set('sets', []);

    $parameters->set('file_extensions', ['php']);

    $parameters->set('exclude_files', []);

    $parameters->set('env(TEST_SUFFIX)', '');
};
