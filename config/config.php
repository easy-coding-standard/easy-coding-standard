<?php

declare (strict_types=1);
namespace ECSPrefix202312;

use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->indentation(Option::INDENTATION_SPACES);
    $ecsConfig->lineEnding(\PHP_EOL);
    $cacheDirectory = \sys_get_temp_dir() . '/changed_files_detector';
    if (StaticVersionResolver::PACKAGE_VERSION !== '@package_version@') {
        $cacheDirectory .= '_' . StaticVersionResolver::PACKAGE_VERSION;
    }
    $ecsConfig->cacheDirectory($cacheDirectory);
    // make cache individual per project
    $cacheNamespace = \str_replace(\DIRECTORY_SEPARATOR, '_', \getcwd());
    $ecsConfig->cacheNamespace($cacheNamespace);
    $ecsConfig->parallel();
    $ecsConfig->paths([]);
    $ecsConfig->skip([]);
    $ecsConfig->fileExtensions(['php']);
};
