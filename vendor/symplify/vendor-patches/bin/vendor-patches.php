<?php

declare (strict_types=1);
namespace ECSPrefix20220520;

use ECSPrefix20220520\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
use ECSPrefix20220520\Symplify\VendorPatches\Kernel\VendorPatchesKernel;
$possibleAutoloadPaths = [__DIR__ . '/../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php', __DIR__ . '/../../../vendor/autoload.php'];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (!\file_exists($possibleAutoloadPath)) {
        continue;
    }
    require_once $possibleAutoloadPath;
}
$kernelBootAndApplicationRun = new \ECSPrefix20220520\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ECSPrefix20220520\Symplify\VendorPatches\Kernel\VendorPatchesKernel::class);
$kernelBootAndApplicationRun->run();
