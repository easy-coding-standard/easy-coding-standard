<?php

declare (strict_types=1);
namespace ECSPrefix20210525\Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use ECSPrefix20210525\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210525\Symfony\Component\DependencyInjection\ContainerBuilder;
final class AutowireInterfacesCompilerPass implements \ECSPrefix20210525\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @var string[]
     */
    private $typesToAutowire = [];
    /**
     * @param string[] $typesToAutowire
     */
    public function __construct(array $typesToAutowire)
    {
        $this->typesToAutowire = $typesToAutowire;
    }
    /**
     * @return void
     */
    public function process(\ECSPrefix20210525\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $definition) {
            foreach ($this->typesToAutowire as $typeToAutowire) {
                if (!\is_a((string) $definition->getClass(), $typeToAutowire, \true)) {
                    continue;
                }
                $definition->setAutowired(\true);
                continue 2;
            }
        }
    }
}
