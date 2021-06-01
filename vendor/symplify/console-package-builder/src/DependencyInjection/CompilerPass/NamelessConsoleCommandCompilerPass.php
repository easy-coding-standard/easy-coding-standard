<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass;

use ConfigTransformer20210601\Symfony\Component\Console\Command\Command;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command\CommandNaming;
/**
 * @see \Symplify\ConsolePackageBuilder\Tests\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPassTest
 */
final class NamelessConsoleCommandCompilerPass implements \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $definitionClass = $definition->getClass();
            if ($definitionClass === null) {
                continue;
            }
            if (!\is_a($definitionClass, \ConfigTransformer20210601\Symfony\Component\Console\Command\Command::class, \true)) {
                continue;
            }
            $commandName = \ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName($definitionClass);
            $definition->addMethodCall('setName', [$commandName]);
        }
    }
}
