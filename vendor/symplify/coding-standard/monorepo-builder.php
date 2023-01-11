<?php

declare (strict_types=1);
namespace ECSPrefix202301;

use ECSPrefix202301\Symplify\MonorepoBuilder\Config\MBConfig;
use ECSPrefix202301\Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use ECSPrefix202301\Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
return static function (MBConfig $mbConfig) : void {
    // @see https://github.com/symplify/monorepo-builder#6-release-flow
    $mbConfig->workers([TagVersionReleaseWorker::class, PushTagReleaseWorker::class]);
};
