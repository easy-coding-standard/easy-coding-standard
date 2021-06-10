<?php

declare (strict_types=1);
namespace ECSPrefix20210610;

use ECSPrefix20210610\Symplify\EasyTesting\HttpKernel\EasyTestingKernel;
use ECSPrefix20210610\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
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
$kernelBootAndApplicationRun = new \ECSPrefix20210610\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ECSPrefix20210610\Symplify\EasyTesting\HttpKernel\EasyTestingKernel::class);
$kernelBootAndApplicationRun->run();
