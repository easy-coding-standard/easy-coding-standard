<?php

namespace ECSPrefix20210514\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass;

use ECSPrefix20210514\Symfony\Component\Console\Command\Command;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210514\Symplify\PackageBuilder\Console\Command\CommandNaming;
/**
 * @see \Symplify\ConsolePackageBuilder\Tests\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPassTest
 */
final class NamelessConsoleCommandCompilerPass implements \ECSPrefix20210514\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(\ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $definitionClass = $definition->getClass();
            if ($definitionClass === null) {
                continue;
            }
            if (!\is_a($definitionClass, \ECSPrefix20210514\Symfony\Component\Console\Command\Command::class, \true)) {
                continue;
            }
            $commandName = \ECSPrefix20210514\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName($definitionClass);
            $definition->addMethodCall('setName', [$commandName]);
        }
    }
}
