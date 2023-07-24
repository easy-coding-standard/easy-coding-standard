<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(__DIR__ . '/common/array.php');
    $ecsConfig->import(__DIR__ . '/common/comments.php');
    $ecsConfig->import(__DIR__ . '/common/control-structures.php');
    $ecsConfig->import(__DIR__ . '/common/docblock.php');
    $ecsConfig->import(__DIR__ . '/common/namespaces.php');
    $ecsConfig->import(__DIR__ . '/common/phpunit.php');
    $ecsConfig->import(__DIR__ . '/common/spaces.php');
    $ecsConfig->import(__DIR__ . '/common/strict.php');
};
