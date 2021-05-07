<?php

namespace Symplify\EasyCodingStandard\Bundle;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\EasyCodingStandardExtension;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
final class EasyCodingStandardBundle extends Bundle
{
    /**
     * Order of compiler passes matters!
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function build($containerBuilder)
    {
        // cleanup
        $containerBuilder->addCompilerPass(new RemoveExcludedCheckersCompilerPass());
        $containerBuilder->addCompilerPass(new RemoveMutualCheckersCompilerPass());
        // autowire
        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([FixerInterface::class, Sniff::class, OutputFormatterInterface::class]));
        // exceptions
        $containerBuilder->addCompilerPass(new ConflictingCheckersCompilerPass());
        // method calls
        $containerBuilder->addCompilerPass(new FixerWhitespaceConfigCompilerPass());
    }
    /**
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new EasyCodingStandardExtension();
    }
}
