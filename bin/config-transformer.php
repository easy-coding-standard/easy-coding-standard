<?php

declare (strict_types=1);
namespace ConfigTransformer20210601;

use ConfigTransformer20210601\Symplify\ConfigTransformer\HttpKernel\ConfigTransformerKernel;
use ConfigTransformer20210601\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
// autoload local project path, if not installed ad vendor dependency
$projectVendorAutoload = \getcwd() . '/vendor/autoload';
if (\file_exists($projectVendorAutoload)) {
    require_once $projectVendorAutoload;
}
$codeSnifferAutoload = \getcwd() . '/vendor/squizlabs/php_codesniffer/autoload.php';
if (\file_exists($codeSnifferAutoload)) {
    require_once $codeSnifferAutoload;
}
$kernelBootAndApplicationRun = new \ConfigTransformer20210601\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\ConfigTransformer20210601\Symplify\ConfigTransformer\HttpKernel\ConfigTransformerKernel::class);
$kernelBootAndApplicationRun->run();
