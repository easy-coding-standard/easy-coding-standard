<?php

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AutowireInterfacesCompilerPass implements CompilerPassInterface
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
    public function process(ContainerBuilder $containerBuilder)
    {
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $definition) {
            foreach ($this->typesToAutowire as $typeToAutowire) {
                if (! is_a((string) $definition->getClass(), $typeToAutowire, true)) {
                    continue;
                }

                $definition->setAutowired(true);
                continue 2;
            }
        }
    }
}
