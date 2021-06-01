<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection;

use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\Yaml\Yaml;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\Extension\AliasConfigurableExtension;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey;
/**
 * This fakes basic extensions, so loading of config is possible without loading real extensions and booting your whole
 * project
 */
final class ExtensionFaker
{
    /**
     * @var YamlKey
     */
    private $yamlKey;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey $yamlKey)
    {
        $this->yamlKey = $yamlKey;
    }
    /**
     * @return void
     */
    public function fakeInContainerBuilder(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $yamlContent)
    {
        $yaml = \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::parse($yamlContent, \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::PARSE_CUSTOM_TAGS);
        // empty file
        if ($yaml === null) {
            return;
        }
        $rootKeys = \array_keys($yaml);
        /** @var string[] $extensionKeys */
        $extensionKeys = \array_diff($rootKeys, $this->yamlKey->provideRootKeys());
        if ($extensionKeys === []) {
            return;
        }
        foreach ($extensionKeys as $extensionKey) {
            $aliasConfigurableExtension = new \ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\Extension\AliasConfigurableExtension($extensionKey);
            $containerBuilder->registerExtension($aliasConfigurableExtension);
        }
    }
}
