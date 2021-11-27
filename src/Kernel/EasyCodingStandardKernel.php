<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Kernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix20211127\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20211127\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20211127\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use ECSPrefix20211127\Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig;
use ECSPrefix20211127\Symplify\EasyParallel\ValueObject\EasyParallelConfig;
use ECSPrefix20211127\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use ECSPrefix20211127\Symplify\Skipper\ValueObject\SkipperConfig;
use ECSPrefix20211127\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends \ECSPrefix20211127\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211127\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $compilerPasses = $this->createCompilerPasses();
        $configFiles[] = \ECSPrefix20211127\Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = \ECSPrefix20211127\Symplify\Skipper\ValueObject\SkipperConfig::FILE_PATH;
        $configFiles[] = \Symplify\CodingStandard\ValueObject\CodingStandardConfig::FILE_PATH;
        $configFiles[] = \Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig::FILE_PATH;
        $configFiles[] = \ECSPrefix20211127\Symplify\EasyParallel\ValueObject\EasyParallelConfig::FILE_PATH;
        return $this->create([], $compilerPasses, $configFiles);
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
        $compilerPasses[] = new \ECSPrefix20211127\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass([\PhpCsFixer\Fixer\FixerInterface::class, \PHP_CodeSniffer\Sniffs\Sniff::class, \Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface::class]);
        $compilerPasses[] = new \Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass();
        $compilerPasses[] = new \ECSPrefix20211127\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        return $compilerPasses;
    }
}
