<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddSysGetTempDirParameterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->setParameter('sys_get_temp_dir', sys_get_temp_dir());
    }
}
