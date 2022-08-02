<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Kernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix202208\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix202208\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix202208\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig;
use ECSPrefix202208\Symplify\EasyParallel\ValueObject\EasyParallelConfig;
use ECSPrefix202208\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use ECSPrefix202208\Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig;
use ECSPrefix202208\Symplify\Skipper\ValueObject\SkipperConfig;
use ECSPrefix202208\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix202208\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $compilerPasses = $this->createCompilerPasses();
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = SkipperConfig::FILE_PATH;
        $configFiles[] = CodingStandardConfig::FILE_PATH;
        $configFiles[] = EasyCodingStandardConfig::FILE_PATH;
        $configFiles[] = EasyParallelConfig::FILE_PATH;
        return $this->create($configFiles, $compilerPasses, []);
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
            new AutowireInterfacesCompilerPass([FixerInterface::class, Sniff::class, OutputFormatterInterface::class]),
            new FixerWhitespaceConfigCompilerPass(),
            new AutowireArrayParameterCompilerPass(),
        ];
    }
}
