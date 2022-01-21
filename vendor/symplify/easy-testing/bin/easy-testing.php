<?php

declare (strict_types=1);
namespace ECSPrefix20220121;

use ECSPrefix20220121\Symplify\EasyTesting\Kernel\EasyTestingKernel;
use ECSPrefix20220121\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
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
$kernelBootAndApplicationRun = new \ECSPrefix20220121\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ECSPrefix20220121\Symplify\EasyTesting\Kernel\EasyTestingKernel::class);
$kernelBootAndApplicationRun->run();
