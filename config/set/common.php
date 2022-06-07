<?php

declare (strict_types=1);
namespace ECSPrefix20220607;

use ECSPrefix20220607\Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->import(__DIR__ . '/common/*.php');
};
