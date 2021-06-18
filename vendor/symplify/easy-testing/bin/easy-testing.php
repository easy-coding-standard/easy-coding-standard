<?php

declare (strict_types=1);
namespace ECSPrefix20210618;

use ECSPrefix20210618\Symplify\EasyTesting\HttpKernel\EasyTestingKernel;
use ECSPrefix20210618\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
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
$kernelBootAndApplicationRun = new \ECSPrefix20210618\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ECSPrefix20210618\Symplify\EasyTesting\HttpKernel\EasyTestingKernel::class);
$kernelBootAndApplicationRun->run();
