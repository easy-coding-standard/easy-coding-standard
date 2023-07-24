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
    private ?Container $container = null;

    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $defaultConfig = __DIR__ . '/../../config/config.php';

        // default config must be merged as first, to allow custom configs to override parameters
        $configFiles = array_merge([$defaultConfig], $configFiles);

        $compilerPasses = $this->createCompilerPasses();

        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = CodingStandardConfig::FILE_PATH;
        $configFiles[] = EasyParallelConfig::FILE_PATH;

        return $this->create($configFiles, $compilerPasses);
    }

    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function create(array $configFiles, array $compilerPasses = []): ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory();

        // @todo
        // $configFiles[] = SymplifyKernelConfig::FILE_PATH;

        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses);
        $containerBuilder->compile();

        $this->container = $containerBuilder;

        return $containerBuilder;
    }

    /**
     * @return CompilerPassInterface[]
     */
    private function createCompilerPasses(): array
    {
        return [
            // cleanup
            new RemoveExcludedCheckersCompilerPass(),
            new RemoveMutualCheckersCompilerPass(),
            new ConflictingCheckersCompilerPass(),
            // autowire
            new FixerWhitespaceConfigCompilerPass(),
        ];
    }
}
