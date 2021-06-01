<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\Psr\Container\ContainerInterface as PsrContainerInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\SymfonyVersionFeature;
use ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
final class ContainerBuilderCleaner
{
    /**
     * @see https://regex101.com/r/0qo8RA/1
     * @var string
     */
    const ANONYMOUS_CLASS_REGEX = '#^[\\d]+\\_[\\w]{64}$#';
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;
    /**
     * @var Configuration
     */
    private $configuration;
    public function __construct(\ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\PrivatesAccessor $privatesAccessor, \ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration $configuration)
    {
        $this->privatesAccessor = $privatesAccessor;
        $this->configuration = $configuration;
    }
    /**
     * @return void
     */
    public function cleanContainerBuilder(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $this->removeExplicitPrivate($containerBuilder);
        $this->removeSymfonyInternalServices($containerBuilder);
        $this->removeTemporaryAnonymousIds($containerBuilder);
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $this->resolvePolyfillForNameTag($definition);
        }
    }
    /**
     * @return void
     */
    private function removeSymfonyInternalServices(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->removeDefinition('service_container');
        $containerBuilder->removeAlias(\ConfigTransformer20210601\Psr\Container\ContainerInterface::class);
        $containerBuilder->removeAlias(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerInterface::class);
    }
    /**
     * @return void
     */
    private function removeExplicitPrivate(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            // remove public: false, by default
            if ($definition->isPublic()) {
                continue;
            }
            $definition->setPrivate(\true);
        }
    }
    /**
     * @return void
     */
    private function removeTemporaryAnonymousIds(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $definitions = $this->privatesAccessor->getPrivateProperty($containerBuilder, 'definitions');
        foreach ($definitions as $name => $definition) {
            if (!\is_string($name)) {
                continue;
            }
            if (!$this->isGeneratedKeyForAnonymousClass($name)) {
                continue;
            }
            unset($definitions[$name]);
            $definitions[] = $definition;
        }
        $this->privatesAccessor->setPrivateProperty($containerBuilder, 'definitions', $definitions);
    }
    private function isGeneratedKeyForAnonymousClass(string $name) : bool
    {
        return (bool) \ConfigTransformer20210601\Nette\Utils\Strings::match($name, self::ANONYMOUS_CLASS_REGEX);
    }
    /**
     * @return void
     */
    private function resolvePolyfillForNameTag(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition $definition)
    {
        if ($definition->getTags() === []) {
            return;
        }
        $tags = $definition->getTags();
        foreach ($definition->getTags() as $name => $value) {
            /** @var mixed[] $tagValues */
            $tagValues = $value[0];
            if ($this->shouldSkipNameTagInlining($tagValues)) {
                continue;
            }
            unset($tags[$name]);
            $tagValues = [];
            foreach ($value as $singleValue) {
                $singleTag = \array_merge(['name' => $name], $singleValue);
                $tagValues[] = $singleTag;
            }
            $tags[] = $tagValues;
        }
        $definition->setTags($tags);
    }
    private function shouldSkipNameTagInlining(array $tagValues) : bool
    {
        if (\count($tagValues) !== 0) {
            return \false;
        }
        return $this->configuration->isAtLeastSymfonyVersion(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\SymfonyVersionFeature::TAGS_WITHOUT_NAME);
    }
}
