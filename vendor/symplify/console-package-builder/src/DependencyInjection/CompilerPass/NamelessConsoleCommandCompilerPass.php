<?php

declare (strict_types=1);
namespace ECSPrefix20210710\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass;

use ECSPrefix20210710\Symfony\Component\Console\Command\Command;
use ECSPrefix20210710\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210710\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210710\Symplify\PackageBuilder\Console\Command\CommandNaming;
/**
 * @see \Symplify\ConsolePackageBuilder\Tests\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPassTest
 */
final class NamelessConsoleCommandCompilerPass implements \ECSPrefix20210710\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @return void
     */
    public function process($containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $definitionClass = $definition->getClass();
            if ($definitionClass === null) {
                continue;
            }
            if (!\is_a($definitionClass, \ECSPrefix20210710\Symfony\Component\Console\Command\Command::class, \true)) {
                continue;
            }
            $commandName = \ECSPrefix20210710\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName($definitionClass);
            $definition->addMethodCall('setName', [$commandName]);
        }
    }
}
