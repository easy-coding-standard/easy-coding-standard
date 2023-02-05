<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        PHPUnitSetList::PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/packages',
        __DIR__ . '/config',
        __DIR__ . '/tests',
        __DIR__ . '/packages-tests',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->bootstrapFiles([__DIR__ . '/tests/bootstrap.php']);

    $rectorConfig->skip(['*/Source/*', '*/Fixture/*', 'packages/SniffRunner/ValueObject/File.php']);
};
