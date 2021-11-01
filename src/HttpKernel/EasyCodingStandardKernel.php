<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\HttpKernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix20211101\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20211101\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20211101\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use ECSPrefix20211101\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\DependencyInjection\Extension\SymplifyCodingStandardExtension;
use ECSPrefix20211101\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\EasyCodingStandardExtension;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use ECSPrefix20211101\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use ECSPrefix20211101\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
use ECSPrefix20211101\Symplify\SymfonyContainerBuilder\ContainerBuilderFactory;
use ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix20211101\Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;
final class EasyCodingStandardKernel implements \ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211101\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $compilerPasses = $this->createCompilerPasses();
        $extensions = $this->createExtensions();
        $containerBuilderFactory = new \ECSPrefix20211101\Symplify\SymfonyContainerBuilder\ContainerBuilderFactory();
        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \ECSPrefix20211101\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof \ECSPrefix20211101\Symfony\Component\DependencyInjection\ContainerInterface) {
            throw new \Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException();
        }
        return $this->container;
    }
    /**
     * @return ExtensionInterface[]
     */
    private function createExtensions() : array
    {
        $extensions = [];
        $extensions[] = new \ECSPrefix20211101\Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension();
        $extensions[] = new \Symplify\EasyCodingStandard\DependencyInjection\Extension\EasyCodingStandardExtension();
        $extensions[] = new \ECSPrefix20211101\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
        $extensions[] = new \ECSPrefix20211101\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
        $extensions[] = new \Symplify\CodingStandard\DependencyInjection\Extension\SymplifyCodingStandardExtension();
        return $extensions;
    }
    /**
     * @return CompilerPassInterface[]
     */
    private function createCompilerPasses() : array
    {
        $compilerPasses = [];
        // cleanup
        $compilerPasses[] = new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass();
        $compilerPasses[] = new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass();
        $compilerPasses[] = new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass();
        // autowire
        $compilerPasses[] = new \ECSPrefix20211101\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass([\PhpCsFixer\Fixer\FixerInterface::class, \PHP_CodeSniffer\Sniffs\Sniff::class, \Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface::class]);
        $compilerPasses[] = new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass();
        $compilerPasses[] = new \ECSPrefix20211101\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        return $compilerPasses;
    }
}
