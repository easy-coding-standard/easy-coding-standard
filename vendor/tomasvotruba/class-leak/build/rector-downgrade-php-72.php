<?php

declare (strict_types=1);
namespace ECSPrefix202607;

use ECSPrefix202607\Rector\Config\RectorConfig;
use ECSPrefix202607\Rector\Set\ValueObject\DowngradeLevelSetList;
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel(240, 8, 1);
    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_72]);
    $rectorConfig->skip([
        '*/Tests/*',
        '*/tests/*',
        __DIR__ . '/../../tests',
        # missing "optional" dependency and never used here
        '*/symfony/framework-bundle/KernelBrowser.php',
        '*/symfony/http-kernel/HttpKernelBrowser.php',
    ]);
};
