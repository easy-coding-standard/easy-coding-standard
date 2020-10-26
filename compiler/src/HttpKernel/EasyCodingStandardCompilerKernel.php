<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ConsoleColorDiff\ConsoleColorDiffBundle;

final class EasyCodingStandardCompilerKernel extends Kernel
{
    public function getCacheDir(): string
    {
        // manually configured, so it can be replaced in phar
        return sys_get_temp_dir() . '/_ecs_compiler';
    }

    public function getLogDir(): string
    {
        // manually configured, so it can be replaced in phar
        return sys_get_temp_dir() . '/_ecs_compiler_log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ConsoleColorDiffBundle()];
    }
}
