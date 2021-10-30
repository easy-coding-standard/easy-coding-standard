<?php

declare (strict_types=1);
namespace ECSPrefix20211030\Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use ECSPrefix20211030\Symfony\Component\Console\Command\Command;
use ECSPrefix20211030\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20211030\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211030\Symplify\PackageBuilder\Console\Command\CommandNaming;
/**
 * @see \Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPassTest
 */
final class NamelessConsoleCommandCompilerPass implements \ECSPrefix20211030\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function process($containerBuilder) : void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $definitionClass = $definition->getClass();
            if ($definitionClass === null) {
                continue;
            }
            if (!\is_a($definitionClass, \ECSPrefix20211030\Symfony\Component\Console\Command\Command::class, \true)) {
                continue;
            }
            $commandName = \ECSPrefix20211030\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName($definitionClass);
            $definition->addMethodCall('setName', [$commandName]);
        }
    }
}
