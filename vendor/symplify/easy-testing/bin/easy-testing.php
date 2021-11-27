<?php

declare (strict_types=1);
namespace ECSPrefix20211127;

use ECSPrefix20211127\Symplify\EasyTesting\Kernel\EasyTestingKernel;
use ECSPrefix20211127\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
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
$kernelBootAndApplicationRun = new \ECSPrefix20211127\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ECSPrefix20211127\Symplify\EasyTesting\Kernel\EasyTestingKernel::class);
$kernelBootAndApplicationRun->run();
