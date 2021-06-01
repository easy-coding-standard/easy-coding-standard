<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\Cache\Adapter;

use ConfigTransformer20210601\Symfony\Component\Cache\CacheItem;
use ConfigTransformer20210601\Symfony\Component\Cache\Exception\CacheException;
use ConfigTransformer20210601\Symfony\Component\Cache\Marshaller\MarshallerInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ApcuAdapter extends \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AbstractAdapter
{
    private $marshaller;
    /**
     * @throws CacheException if APCu is not enabled
     * @param \Symfony\Component\Cache\Marshaller\MarshallerInterface|null $marshaller
     */
    public function __construct(string $namespace = '', int $defaultLifetime = 0, string $version = null, $marshaller = null)
    {
        if (!static::isSupported()) {
            throw new \ConfigTransformer20210601\Symfony\Component\Cache\Exception\CacheException('APCu is not enabled.');
        }
        if ('cli' === \PHP_SAPI) {
            \ini_set('apc.use_request_time', 0);
        }
        parent::__construct($namespace, $defaultLifetime);
        if (null !== $version) {
            \ConfigTransformer20210601\Symfony\Component\Cache\CacheItem::validateKey($version);
            if (!\apcu_exists($version . '@' . $namespace)) {
                $this->doClear($namespace);
                \apcu_add($version . '@' . $namespace, null);
            }
        }
        $this->marshaller = $marshaller;
    }
    public static function isSupported()
    {
        return \function_exists('apcu_fetch') && \filter_var(\ini_get('apc.enabled'), \FILTER_VALIDATE_BOOLEAN);
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch(array $ids)
    {
        $unserializeCallbackHandler = \ini_set('unserialize_callback_func', __CLASS__ . '::handleUnserializeCallback');
        try {
            $values = [];
            foreach (\apcu_fetch($ids, $ok) ?: [] as $k => $v) {
                if (null !== $v || $ok) {
                    $values[$k] = null !== $this->marshaller ? $this->marshaller->unmarshall($v) : $v;
                }
            }
            return $values;
        } catch (\Error $e) {
            throw new \ErrorException($e->getMessage(), $e->getCode(), \E_ERROR, $e->getFile(), $e->getLine());
        } finally {
            \ini_set('unserialize_callback_func', $unserializeCallbackHandler);
        }
    }
    /**
     * {@inheritdoc}
     * @param string $id
     */
    protected function doHave($id)
    {
        return \apcu_exists($id);
    }
    /**
     * {@inheritdoc}
     * @param string $namespace
     */
    protected function doClear($namespace)
    {
        return isset($namespace[0]) && \class_exists(\APCuIterator::class, \false) && ('cli' !== \PHP_SAPI || \filter_var(\ini_get('apc.enable_cli'), \FILTER_VALIDATE_BOOLEAN)) ? \apcu_delete(new \APCuIterator(\sprintf('/^%s/', \preg_quote($namespace, '/')), \APC_ITER_KEY)) : \apcu_clear_cache();
    }
    /**
     * {@inheritdoc}
     */
    protected function doDelete(array $ids)
    {
        foreach ($ids as $id) {
            \apcu_delete($id);
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param int $lifetime
     */
    protected function doSave(array $values, $lifetime)
    {
        if (null !== $this->marshaller && !($values = $this->marshaller->marshall($values, $failed))) {
            return $failed;
        }
        try {
            if (\false === ($failures = \apcu_store($values, null, $lifetime))) {
                $failures = $values;
            }
            return \array_keys($failures);
        } catch (\Throwable $e) {
            if (1 === \count($values)) {
                // Workaround https://github.com/krakjoe/apcu/issues/170
                \apcu_delete(\key($values));
            }
            throw $e;
        }
    }
}
