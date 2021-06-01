<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\Cache\DependencyInjection;

use ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AbstractAdapter;
use ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ArrayAdapter;
use ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ChainAdapter;
use ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ParameterNormalizer;
use ConfigTransformer20210601\Symfony\Component\Cache\Messenger\EarlyExpirationDispatcher;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ChildDefinition;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CachePoolPass implements \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $cachePoolTag;
    private $kernelResetTag;
    private $cacheClearerId;
    private $cachePoolClearerTag;
    private $cacheSystemClearerId;
    private $cacheSystemClearerTag;
    private $reverseContainerId;
    private $reversibleTag;
    private $messageHandlerId;
    public function __construct(string $cachePoolTag = 'cache.pool', string $kernelResetTag = 'kernel.reset', string $cacheClearerId = 'cache.global_clearer', string $cachePoolClearerTag = 'cache.pool.clearer', string $cacheSystemClearerId = 'cache.system_clearer', string $cacheSystemClearerTag = 'kernel.cache_clearer', string $reverseContainerId = 'reverse_container', string $reversibleTag = 'container.reversible', string $messageHandlerId = 'cache.early_expiration_handler')
    {
        if (0 < \func_num_args()) {
            trigger_deprecation('symfony/cache', '5.3', 'Configuring "%s" is deprecated.', __CLASS__);
        }
        $this->cachePoolTag = $cachePoolTag;
        $this->kernelResetTag = $kernelResetTag;
        $this->cacheClearerId = $cacheClearerId;
        $this->cachePoolClearerTag = $cachePoolClearerTag;
        $this->cacheSystemClearerId = $cacheSystemClearerId;
        $this->cacheSystemClearerTag = $cacheSystemClearerTag;
        $this->reverseContainerId = $reverseContainerId;
        $this->reversibleTag = $reversibleTag;
        $this->messageHandlerId = $messageHandlerId;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if ($container->hasParameter('cache.prefix.seed')) {
            $seed = $container->getParameterBag()->resolveValue($container->getParameter('cache.prefix.seed'));
        } else {
            $seed = '_' . $container->getParameter('kernel.project_dir');
            $seed .= '.' . $container->getParameter('kernel.container_class');
        }
        $needsMessageHandler = \false;
        $allPools = [];
        $clearers = [];
        $attributes = ['provider', 'name', 'namespace', 'default_lifetime', 'early_expiration_message_bus', 'reset'];
        foreach ($container->findTaggedServiceIds($this->cachePoolTag) as $id => $tags) {
            $adapter = $pool = $container->getDefinition($id);
            if ($pool->isAbstract()) {
                continue;
            }
            $class = $adapter->getClass();
            while ($adapter instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ChildDefinition) {
                $adapter = $container->findDefinition($adapter->getParent());
                $class = $class ?: $adapter->getClass();
                if ($t = $adapter->getTag($this->cachePoolTag)) {
                    $tags[0] += $t[0];
                }
            }
            $name = $tags[0]['name'] ?? $id;
            if (!isset($tags[0]['namespace'])) {
                $namespaceSeed = $seed;
                if (null !== $class) {
                    $namespaceSeed .= '.' . $class;
                }
                $tags[0]['namespace'] = $this->getNamespace($namespaceSeed, $name);
            }
            if (isset($tags[0]['clearer'])) {
                $clearer = $tags[0]['clearer'];
                while ($container->hasAlias($clearer)) {
                    $clearer = (string) $container->getAlias($clearer);
                }
            } else {
                $clearer = null;
            }
            unset($tags[0]['clearer'], $tags[0]['name']);
            if (isset($tags[0]['provider'])) {
                $tags[0]['provider'] = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference(static::getServiceProvider($container, $tags[0]['provider']));
            }
            if (\ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ChainAdapter::class === $class) {
                $adapters = [];
                foreach ($adapter->getArgument(0) as $provider => $adapter) {
                    if ($adapter instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ChildDefinition) {
                        $chainedPool = $adapter;
                    } else {
                        $chainedPool = $adapter = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ChildDefinition($adapter);
                    }
                    $chainedTags = [\is_int($provider) ? [] : ['provider' => $provider]];
                    $chainedClass = '';
                    while ($adapter instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ChildDefinition) {
                        $adapter = $container->findDefinition($adapter->getParent());
                        $chainedClass = $chainedClass ?: $adapter->getClass();
                        if ($t = $adapter->getTag($this->cachePoolTag)) {
                            $chainedTags[0] += $t[0];
                        }
                    }
                    if (\ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ChainAdapter::class === $chainedClass) {
                        throw new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid service "%s": chain of adapters cannot reference another chain, found "%s".', $id, $chainedPool->getParent()));
                    }
                    $i = 0;
                    if (isset($chainedTags[0]['provider'])) {
                        $chainedPool->replaceArgument($i++, new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference(static::getServiceProvider($container, $chainedTags[0]['provider'])));
                    }
                    if (isset($tags[0]['namespace']) && \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ArrayAdapter::class !== $adapter->getClass()) {
                        $chainedPool->replaceArgument($i++, $tags[0]['namespace']);
                    }
                    if (isset($tags[0]['default_lifetime'])) {
                        $chainedPool->replaceArgument($i++, $tags[0]['default_lifetime']);
                    }
                    $adapters[] = $chainedPool;
                }
                $pool->replaceArgument(0, $adapters);
                unset($tags[0]['provider'], $tags[0]['namespace']);
                $i = 1;
            } else {
                $i = 0;
            }
            foreach ($attributes as $attr) {
                if (!isset($tags[0][$attr])) {
                    // no-op
                } elseif ('reset' === $attr) {
                    if ($tags[0][$attr]) {
                        $pool->addTag($this->kernelResetTag, ['method' => $tags[0][$attr]]);
                    }
                } elseif ('early_expiration_message_bus' === $attr) {
                    $needsMessageHandler = \true;
                    $pool->addMethodCall('setCallbackWrapper', [(new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition(\ConfigTransformer20210601\Symfony\Component\Cache\Messenger\EarlyExpirationDispatcher::class))->addArgument(new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference($tags[0]['early_expiration_message_bus']))->addArgument(new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference($this->reverseContainerId))->addArgument((new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition('callable'))->setFactory([new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference($id), 'setCallbackWrapper'])->addArgument(null))]);
                    $pool->addTag($this->reversibleTag);
                } elseif ('namespace' !== $attr || \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ArrayAdapter::class !== $class) {
                    $argument = $tags[0][$attr];
                    if ('default_lifetime' === $attr && !\is_numeric($argument)) {
                        $argument = (new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition('int', [$argument]))->setFactory([\ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ParameterNormalizer::class, 'normalizeDuration']);
                    }
                    $pool->replaceArgument($i++, $argument);
                }
                unset($tags[0][$attr]);
            }
            if (!empty($tags[0])) {
                throw new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid "%s" tag for service "%s": accepted attributes are "clearer", "provider", "name", "namespace", "default_lifetime", "early_expiration_message_bus" and "reset", found "%s".', $this->cachePoolTag, $id, \implode('", "', \array_keys($tags[0]))));
            }
            if (null !== $clearer) {
                $clearers[$clearer][$name] = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference($id, $container::IGNORE_ON_UNINITIALIZED_REFERENCE);
            }
            $allPools[$name] = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference($id, $container::IGNORE_ON_UNINITIALIZED_REFERENCE);
        }
        if (!$needsMessageHandler) {
            $container->removeDefinition($this->messageHandlerId);
        }
        $notAliasedCacheClearerId = $this->cacheClearerId;
        while ($container->hasAlias($this->cacheClearerId)) {
            $this->cacheClearerId = (string) $container->getAlias($this->cacheClearerId);
        }
        if ($container->hasDefinition($this->cacheClearerId)) {
            $clearers[$notAliasedCacheClearerId] = $allPools;
        }
        foreach ($clearers as $id => $pools) {
            $clearer = $container->getDefinition($id);
            if ($clearer instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ChildDefinition) {
                $clearer->replaceArgument(0, $pools);
            } else {
                $clearer->setArgument(0, $pools);
            }
            $clearer->addTag($this->cachePoolClearerTag);
            if ($this->cacheSystemClearerId === $id) {
                $clearer->addTag($this->cacheSystemClearerTag);
            }
        }
        if ($container->hasDefinition('console.command.cache_pool_list')) {
            $container->getDefinition('console.command.cache_pool_list')->replaceArgument(0, \array_keys($allPools));
        }
    }
    private function getNamespace(string $seed, string $id)
    {
        return \substr(\str_replace('/', '-', \base64_encode(\hash('sha256', $id . $seed, \true))), 0, 10);
    }
    /**
     * @internal
     */
    public static function getServiceProvider(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $container, $name)
    {
        $container->resolveEnvPlaceholders($name, null, $usedEnvs);
        if ($usedEnvs || \preg_match('#^[a-z]++:#', $name)) {
            $dsn = $name;
            if (!$container->hasDefinition($name = '.cache_connection.' . \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder::hash($dsn))) {
                $definition = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition(\ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AbstractAdapter::class);
                $definition->setPublic(\false);
                $definition->setFactory([\ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AbstractAdapter::class, 'createConnection']);
                $definition->setArguments([$dsn, ['lazy' => \true]]);
                $container->setDefinition($name, $definition);
            }
        }
        return $name;
    }
}
