<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    // A. monorepo
    $ecsConfig->import(__DIR__ . '/../../../coding-standard/config/symplify.php', null, 'not_found');
    // B. installed as dependency
    $ecsConfig->import(__DIR__ . '/../../vendor/symplify/coding-standard/config/symplify.php', null, 'not_found');
};
