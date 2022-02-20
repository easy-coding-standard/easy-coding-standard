<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\SymplifyKernel;

use ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use ECSPrefix20220220\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
use ECSPrefix20220220\Symplify\SymplifyKernel\DependencyInjection\LoadExtensionConfigsCompilerPass;
use ECSPrefix20220220\Webmozart\Assert\Assert;
/**
 * @see \Symplify\SymplifyKernel\Tests\ContainerBuilderFactory\ContainerBuilderFactoryTest
 */
final class ContainerBuilderFactory
{
    /**
     * @var \Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface
     */
    private $loaderFactory;
    public function __construct(\ECSPrefix20220220\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface $loaderFactory)
    {
        $this->loaderFactory = $loaderFactory;
    }
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses, array $extensions) : \ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder
    {
        \ECSPrefix20220220\Webmozart\Assert\Assert::allIsAOf($extensions, \ECSPrefix20220220\Symfony\Component\DependencyInjection\Extension\ExtensionInterface::class);
        \ECSPrefix20220220\Webmozart\Assert\Assert::allIsAOf($compilerPasses, \ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface::class);
        \ECSPrefix20220220\Webmozart\Assert\Assert::allString($configFiles);
        \ECSPrefix20220220\Webmozart\Assert\Assert::allFile($configFiles);
        $containerBuilder = new \ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder();
        $this->registerExtensions($containerBuilder, $extensions);
        $this->registerConfigFiles($containerBuilder, $configFiles);
        $this->registerCompilerPasses($containerBuilder, $compilerPasses);
        // this calls load() method in every extensions
        // ensure these extensions are implicitly loaded
        $compilerPassConfig = $containerBuilder->getCompilerPassConfig();
        $compilerPassConfig->setMergePass(new \ECSPrefix20220220\Symplify\SymplifyKernel\DependencyInjection\LoadExtensionConfigsCompilerPass());
        return $containerBuilder;
    }
    /**
     * @param ExtensionInterface[] $extensions
     */
    private function registerExtensions(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, array $extensions) : void
    {
        foreach ($extensions as $extension) {
            $containerBuilder->registerExtension($extension);
        }
    }
    /**
     * @param CompilerPassInterface[] $compilerPasses
     */
    private function registerCompilerPasses(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, array $compilerPasses) : void
    {
        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }
    }
    /**
     * @param string[] $configFiles
     */
    private function registerConfigFiles(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, array $configFiles) : void
    {
        $delegatingLoader = $this->loaderFactory->create($containerBuilder, \getcwd());
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }
    }
}
