<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\ValueObject\Option;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/packages.php');
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::INDENTATION, \Symplify\EasyCodingStandard\ValueObject\Option::INDENTATION_SPACES);
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::LINE_ENDING, \PHP_EOL);
    $cacheDirectory = \sys_get_temp_dir() . '/changed_files_detector%env(TEST_SUFFIX)%';
    if (\Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION !== '@package_version@') {
        $cacheDirectory .= '_' . \Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION;
    }
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::CACHE_DIRECTORY, $cacheDirectory);
    $cacheNamespace = \str_replace(\DIRECTORY_SEPARATOR, '_', \getcwd());
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::CACHE_NAMESPACE, $cacheNamespace);
    // parallel
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL, \true);
    // how many files are processed in single process
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_JOB_SIZE, 60);
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_MAX_NUMBER_OF_PROCESSES, 16);
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_TIMEOUT_IN_SECONDS, 120);
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS, []);
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::FILE_EXTENSIONS, ['php']);
    $parameters->set('env(TEST_SUFFIX)', '');
};
