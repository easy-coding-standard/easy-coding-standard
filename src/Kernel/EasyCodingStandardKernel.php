<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Kernel;

use ECSPrefix202306\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Container;
use ECSPrefix202306\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use ECSPrefix202306\Symplify\EasyParallel\ValueObject\EasyParallelConfig;
use ECSPrefix202306\Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig;
use ECSPrefix202306\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix202306\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use ECSPrefix202306\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
final class EasyCodingStandardKernel implements LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container;
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix202306\Psr\Container\ContainerInterface
    {
        $defaultConfig = __DIR__ . '/../../config/config.php';
        // default config must be merged as first, to allow custom configs to override parameters
        $configFiles = \array_merge([$defaultConfig], $configFiles);
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
    public function create(array $configFiles, array $compilerPasses = []) : ContainerInterface
    {
        $containerBuilderFactory = new \Symplify\EasyCodingStandard\Kernel\ContainerBuilderFactory();
        $configFiles[] = SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \ECSPrefix202306\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof Container) {
            throw new ShouldNotHappenException();
        }
        return $this->container;
    }
    /**
     * @return CompilerPassInterface[]
     */
    private function createCompilerPasses() : array
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
