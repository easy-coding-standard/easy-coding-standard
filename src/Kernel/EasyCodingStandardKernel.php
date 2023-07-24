<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Kernel;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyParallel\ValueObject\EasyParallelConfig;
use Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig;

final class EasyCodingStandardKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        // @todo move
        $compilerPasses = [
            // cleanup
            new RemoveExcludedCheckersCompilerPass(),
            new RemoveMutualCheckersCompilerPass(),
            new ConflictingCheckersCompilerPass(),
            // autowire
            new FixerWhitespaceConfigCompilerPass(),
        ];

        // @todo move
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = CodingStandardConfig::FILE_PATH;
        $configFiles[] = EasyParallelConfig::FILE_PATH;
    }
}
