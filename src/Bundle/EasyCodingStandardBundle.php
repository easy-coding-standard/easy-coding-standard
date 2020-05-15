<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Bundle;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
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
     */
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->setParameter('sys_get_temp_dir', sys_get_temp_dir());
        $containerBuilder->setParameter('getcwd_webalized', Strings::webalize(getcwd()));

        // cleanup
        $containerBuilder->addCompilerPass(new RemoveExcludedCheckersCompilerPass());
        $containerBuilder->addCompilerPass(new RemoveMutualCheckersCompilerPass());

        // autowire
        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([
            FixerInterface::class,
            Sniff::class,
            OutputFormatterInterface::class,
        ]));
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());

        // exceptions
        $containerBuilder->addCompilerPass(new ConflictingCheckersCompilerPass());

        // parameters
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());

        // method calls
        $containerBuilder->addCompilerPass(new FixerWhitespaceConfigCompilerPass());
    }

    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new EasyCodingStandardExtension();
    }
}
