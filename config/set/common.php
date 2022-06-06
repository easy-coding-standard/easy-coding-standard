<?php

declare (strict_types=1);
namespace ECSPrefix20220606;

use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->import(__DIR__ . '/common/*.php');
};
