<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Kernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20220220\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig;
use ECSPrefix20220220\Symplify\EasyParallel\ValueObject\EasyParallelConfig;
use ECSPrefix20220220\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use ECSPrefix20220220\Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig;
use ECSPrefix20220220\Symplify\Skipper\ValueObject\SkipperConfig;
use ECSPrefix20220220\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends \ECSPrefix20220220\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220220\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $compilerPasses = $this->createCompilerPasses();
        $configFiles[] = \ECSPrefix20220220\Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = \ECSPrefix20220220\Symplify\Skipper\ValueObject\SkipperConfig::FILE_PATH;
        $configFiles[] = \Symplify\CodingStandard\ValueObject\CodingStandardConfig::FILE_PATH;
        $configFiles[] = \Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig::FILE_PATH;
        $configFiles[] = \ECSPrefix20220220\Symplify\EasyParallel\ValueObject\EasyParallelConfig::FILE_PATH;
        return $this->create($configFiles, $compilerPasses, []);
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
        $compilerPasses[] = new \ECSPrefix20220220\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass([\PhpCsFixer\Fixer\FixerInterface::class, \PHP_CodeSniffer\Sniffs\Sniff::class, \Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface::class]);
        $compilerPasses[] = new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass();
        $compilerPasses[] = new \ECSPrefix20220220\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        return $compilerPasses;
    }
}
