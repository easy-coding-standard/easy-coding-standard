<?php

namespace ECSPrefix20210515;

use ECSPrefix20210515\Symplify\EasyTesting\HttpKernel\EasyTestingKernel;
use ECSPrefix20210515\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
$possibleAutoloadPaths = [
    // dependency
    __DIR__ . '/../../../autoload.php',
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
$kernelBootAndApplicationRun = new \ECSPrefix20210515\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ECSPrefix20210515\Symplify\EasyTesting\HttpKernel\EasyTestingKernel::class);
$kernelBootAndApplicationRun->run();
