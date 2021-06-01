<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PackageBuilder\DependencyInjection;

use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition;
use ConfigTransformer20210601\Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;
use Throwable;
/**
 * @see \Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionFinderTest
 */
final class DefinitionFinder
{
    /**
     * @return Definition[]
     */
    public function findAllByType(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $type) : array
    {
        $definitions = [];
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (!$this->doesClassExists($class)) {
                continue;
            }
            if (\is_a($class, $type, \true)) {
                $definitions[$name] = $definition;
            }
        }
        return $definitions;
    }
    public function getByType(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $type) : \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition
    {
        $definition = $this->getByTypeIfExists($containerBuilder, $type);
        if ($definition !== null) {
            return $definition;
        }
        throw new \ConfigTransformer20210601\Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException(\sprintf('Definition for type "%s" was not found.', $type));
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Definition|null
     */
    private function getByTypeIfExists(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $type)
    {
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (!$this->doesClassExists($class)) {
                continue;
            }
            if (\is_a($class, $type, \true)) {
                return $definition;
            }
        }
        return null;
    }
    private function doesClassExists(string $class) : bool
    {
        try {
            return \class_exists($class);
        } catch (\Throwable $throwable) {
            return \false;
        }
    }
}
