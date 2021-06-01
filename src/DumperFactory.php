<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer;

use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format;
use ConfigTransformer20210601\Symplify\PackageBuilder\Exception\NotImplementedYetException;
final class DumperFactory
{
    public function createFromContainerBuilderAndOutputFormat(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $outputFormat) : \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Dumper\YamlDumper
    {
        if ($outputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML) {
            return new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Dumper\YamlDumper($containerBuilder);
        }
        throw new \ConfigTransformer20210601\Symplify\PackageBuilder\Exception\NotImplementedYetException($outputFormat);
    }
}
