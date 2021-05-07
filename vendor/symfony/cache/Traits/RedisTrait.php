<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Traits;

use ECSPrefix20210507\Predis\Command\Redis\UNLINK;
use ECSPrefix20210507\Predis\Connection\Aggregate\ClusterInterface;
use ECSPrefix20210507\Predis\Connection\Aggregate\RedisCluster;
use ECSPrefix20210507\Predis\Connection\Aggregate\ReplicationInterface;
use ECSPrefix20210507\Predis\Response\Status;
use ECSPrefix20210507\Symfony\Component\Cache\Exception\CacheException;
use ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException;
use ECSPrefix20210507\Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use ECSPrefix20210507\Symfony\Component\Cache\Marshaller\MarshallerInterface;
/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait RedisTrait
{
    private static $defaultConnectionOptions = ['class' => null, 'persistent' => 0, 'persistent_id' => null, 'timeout' => 30, 'read_timeout' => 0, 'retry_interval' => 0, 'tcp_keepalive' => 0, 'lazy' => null, 'redis_cluster' => \false, 'redis_sentinel' => null, 'dbindex' => 0, 'failover' => 'none', 'ssl' => null];
    private $redis;
    private $marshaller;
    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface $redisClient
     * @param \Symfony\Component\Cache\Marshaller\MarshallerInterface|null $marshaller
     * @param string $namespace
     * @param int $defaultLifetime
     */
    private function init($redisClient, $namespace, $defaultLifetime, $marshaller)
    {
        parent::__construct($namespace, $defaultLifetime);
        if (\preg_match('#[^-+_.A-Za-z0-9]#', $namespace, $match)) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('RedisAdapter namespace contains "%s" but only characters in [-+_.A-Za-z0-9] are allowed.', $match[0]));
        }
        if (!$redisClient instanceof \Redis && !$redisClient instanceof \RedisArray && !$redisClient instanceof \RedisCluster && !$redisClient instanceof \ECSPrefix20210507\Predis\ClientInterface && !$redisClient instanceof \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisProxy && !$redisClient instanceof \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisClusterProxy) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('"%s()" expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\\ClientInterface, "%s" given.', __METHOD__, \get_debug_type($redisClient)));
        }
        if ($redisClient instanceof \ECSPrefix20210507\Predis\ClientInterface && $redisClient->getOptions()->exceptions) {
            $options = clone $redisClient->getOptions();
            \Closure::bind(function () {
                $this->options['exceptions'] = \false;
            }, $options, $options)();
            $redisClient = new $redisClient($redisClient->getConnection(), $options);
        }
        $this->redis = $redisClient;
        $this->marshaller = isset($marshaller) ? $marshaller : new \ECSPrefix20210507\Symfony\Component\Cache\Marshaller\DefaultMarshaller();
    }
    /**
     * Creates a Redis connection using a DSN configuration.
     *
     * Example DSN:
     *   - redis://localhost
     *   - redis://example.com:1234
     *   - redis://secret@example.com/13
     *   - redis:///var/run/redis.sock
     *   - redis://secret@/var/run/redis.sock/13
     *
     * @param string $dsn
     * @param array  $options See self::$defaultConnectionOptions
     *
     * @throws InvalidArgumentException when the DSN is invalid
     *
     * @return \Redis|\RedisCluster|RedisClusterProxy|RedisProxy|\Predis\ClientInterface According to the "class" option
     */
    public static function createConnection($dsn, array $options = [])
    {
        if (0 === \strpos($dsn, 'redis:')) {
            $scheme = 'redis';
        } elseif (0 === \strpos($dsn, 'rediss:')) {
            $scheme = 'rediss';
        } else {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Invalid Redis DSN: "%s" does not start with "redis:" or "rediss".', $dsn));
        }
        if (!\extension_loaded('redis') && !\class_exists(\ECSPrefix20210507\Predis\Client::class)) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\CacheException(\sprintf('Cannot find the "redis" extension nor the "predis/predis" package: "%s".', $dsn));
        }
        $params = \preg_replace_callback('#^' . $scheme . ':(//)?(?:(?:[^:@]*+:)?([^@]*+)@)?#', function ($m) use(&$auth) {
            if (isset($m[2])) {
                $auth = $m[2];
                if ('' === $auth) {
                    $auth = null;
                }
            }
            return 'file:' . (isset($m[1]) ? $m[1] : '');
        }, $dsn);
        if (\false === ($params = \parse_url($params))) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Invalid Redis DSN: "%s".', $dsn));
        }
        $query = $hosts = [];
        $tls = 'rediss' === $scheme;
        $tcpScheme = $tls ? 'tls' : 'tcp';
        if (isset($params['query'])) {
            \parse_str($params['query'], $query);
            if (isset($query['host'])) {
                if (!\is_array($hosts = $query['host'])) {
                    throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Invalid Redis DSN: "%s".', $dsn));
                }
                foreach ($hosts as $host => $parameters) {
                    if (\is_string($parameters)) {
                        \parse_str($parameters, $parameters);
                    }
                    if (\false === ($i = \strrpos($host, ':'))) {
                        $hosts[$host] = ['scheme' => $tcpScheme, 'host' => $host, 'port' => 6379] + $parameters;
                    } elseif ($port = (int) \substr($host, 1 + $i)) {
                        $hosts[$host] = ['scheme' => $tcpScheme, 'host' => \substr($host, 0, $i), 'port' => $port] + $parameters;
                    } else {
                        $hosts[$host] = ['scheme' => 'unix', 'path' => \substr($host, 0, $i)] + $parameters;
                    }
                }
                $hosts = \array_values($hosts);
            }
        }
        if (isset($params['host']) || isset($params['path'])) {
            if (!isset($params['dbindex']) && isset($params['path']) && \preg_match('#/(\\d+)$#', $params['path'], $m)) {
                $params['dbindex'] = $m[1];
                $params['path'] = \substr($params['path'], 0, -\strlen($m[0]));
            }
            if (isset($params['host'])) {
                \array_unshift($hosts, ['scheme' => $tcpScheme, 'host' => $params['host'], 'port' => isset($params['port']) ? $params['port'] : 6379]);
            } else {
                \array_unshift($hosts, ['scheme' => 'unix', 'path' => $params['path']]);
            }
        }
        if (!$hosts) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Invalid Redis DSN: "%s".', $dsn));
        }
        $params += $query + $options + self::$defaultConnectionOptions;
        if (isset($params['redis_sentinel']) && !\class_exists(\ECSPrefix20210507\Predis\Client::class)) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\CacheException(\sprintf('Redis Sentinel support requires the "predis/predis" package: "%s".', $dsn));
        }
        if (null === $params['class'] && !isset($params['redis_sentinel']) && \extension_loaded('redis')) {
            $class = $params['redis_cluster'] ? \RedisCluster::class : (1 < \count($hosts) ? \RedisArray::class : \Redis::class);
        } else {
            $class = null === $params['class'] ? \ECSPrefix20210507\Predis\Client::class : $params['class'];
        }
        if (\is_a($class, \Redis::class, \true)) {
            $connect = $params['persistent'] || $params['persistent_id'] ? 'pconnect' : 'connect';
            $redis = new $class();
            $initializer = static function ($redis) use($connect, $params, $dsn, $auth, $hosts, $tls) {
                $host = isset($hosts[0]['host']) ? $hosts[0]['host'] : $hosts[0]['path'];
                $port = isset($hosts[0]['port']) ? $hosts[0]['port'] : null;
                if (isset($hosts[0]['host']) && $tls) {
                    $host = 'tls://' . $host;
                }
                try {
                    @$redis->{$connect}($host, $port, $params['timeout'], (string) $params['persistent_id'], $params['retry_interval'], $params['read_timeout'], ['stream' => isset($params['ssl']) ? $params['ssl'] : null]);
                    \set_error_handler(function ($type, $msg) use(&$error) {
                        $error = $msg;
                    });
                    $isConnected = $redis->isConnected();
                    \restore_error_handler();
                    if (!$isConnected) {
                        $error = \preg_match('/^Redis::p?connect\\(\\): (.*)/', $error, $error) ? \sprintf(' (%s)', $error[1]) : '';
                        throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Redis connection "%s" failed: ', $dsn) . $error . '.');
                    }
                    if (null !== $auth && !$redis->auth($auth) || $params['dbindex'] && !$redis->select($params['dbindex'])) {
                        $e = \preg_replace('/^ERR /', '', $redis->getLastError());
                        throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Redis connection "%s" failed: ', $dsn) . $e . '.');
                    }
                    if (0 < $params['tcp_keepalive'] && \defined('Redis::OPT_TCP_KEEPALIVE')) {
                        $redis->setOption(\Redis::OPT_TCP_KEEPALIVE, $params['tcp_keepalive']);
                    }
                } catch (\RedisException $e) {
                    throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Redis connection "%s" failed: ', $dsn) . $e->getMessage());
                }
                return \true;
            };
            if ($params['lazy']) {
                $redis = new \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisProxy($redis, $initializer);
            } else {
                $initializer($redis);
            }
        } elseif (\is_a($class, \RedisArray::class, \true)) {
            foreach ($hosts as $i => $host) {
                switch ($host['scheme']) {
                    case 'tcp':
                        $hosts[$i] = $host['host'] . ':' . $host['port'];
                        break;
                    case 'tls':
                        $hosts[$i] = 'tls://' . $host['host'] . ':' . $host['port'];
                        break;
                    default:
                        $hosts[$i] = $host['path'];
                }
            }
            $params['lazy_connect'] = isset($params['lazy']) ? $params['lazy'] : \true;
            $params['connect_timeout'] = $params['timeout'];
            try {
                $redis = new $class($hosts, $params);
            } catch (\RedisClusterException $e) {
                throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Redis connection "%s" failed: ', $dsn) . $e->getMessage());
            }
            if (0 < $params['tcp_keepalive'] && \defined('Redis::OPT_TCP_KEEPALIVE')) {
                $redis->setOption(\Redis::OPT_TCP_KEEPALIVE, $params['tcp_keepalive']);
            }
        } elseif (\is_a($class, \RedisCluster::class, \true)) {
            $initializer = static function () use($class, $params, $dsn, $hosts) {
                foreach ($hosts as $i => $host) {
                    switch ($host['scheme']) {
                        case 'tcp':
                            $hosts[$i] = $host['host'] . ':' . $host['port'];
                            break;
                        case 'tls':
                            $hosts[$i] = 'tls://' . $host['host'] . ':' . $host['port'];
                            break;
                        default:
                            $hosts[$i] = $host['path'];
                    }
                }
                try {
                    $redis = new $class(null, $hosts, $params['timeout'], $params['read_timeout'], (bool) $params['persistent'], isset($params['auth']) ? $params['auth'] : '', isset($params['ssl']) ? $params['ssl'] : null);
                } catch (\RedisClusterException $e) {
                    throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Redis connection "%s" failed: ', $dsn) . $e->getMessage());
                }
                if (0 < $params['tcp_keepalive'] && \defined('Redis::OPT_TCP_KEEPALIVE')) {
                    $redis->setOption(\Redis::OPT_TCP_KEEPALIVE, $params['tcp_keepalive']);
                }
                switch ($params['failover']) {
                    case 'error':
                        $redis->setOption(\RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_ERROR);
                        break;
                    case 'distribute':
                        $redis->setOption(\RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_DISTRIBUTE);
                        break;
                    case 'slaves':
                        $redis->setOption(\RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_DISTRIBUTE_SLAVES);
                        break;
                }
                return $redis;
            };
            $redis = $params['lazy'] ? new \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisClusterProxy($initializer) : $initializer();
        } elseif (\is_a($class, \ECSPrefix20210507\Predis\ClientInterface::class, \true)) {
            if ($params['redis_cluster']) {
                $params['cluster'] = 'redis';
                if (isset($params['redis_sentinel'])) {
                    throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Cannot use both "redis_cluster" and "redis_sentinel" at the same time: "%s".', $dsn));
                }
            } elseif (isset($params['redis_sentinel'])) {
                $params['replication'] = 'sentinel';
                $params['service'] = $params['redis_sentinel'];
            }
            $params += ['parameters' => []];
            $params['parameters'] += ['persistent' => $params['persistent'], 'timeout' => $params['timeout'], 'read_write_timeout' => $params['read_timeout'], 'tcp_nodelay' => \true];
            if ($params['dbindex']) {
                $params['parameters']['database'] = $params['dbindex'];
            }
            if (null !== $auth) {
                $params['parameters']['password'] = $auth;
            }
            if (1 === \count($hosts) && !($params['redis_cluster'] || $params['redis_sentinel'])) {
                $hosts = $hosts[0];
            } elseif (\in_array($params['failover'], ['slaves', 'distribute'], \true) && !isset($params['replication'])) {
                $params['replication'] = \true;
                $hosts[0] += ['alias' => 'master'];
            }
            $params['exceptions'] = \false;
            $redis = new $class($hosts, \array_diff_key($params, \array_diff_key(self::$defaultConnectionOptions, ['ssl' => null])));
            if (isset($params['redis_sentinel'])) {
                $redis->getConnection()->setSentinelTimeout($params['timeout']);
            }
        } elseif (\class_exists($class, \false)) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('"%s" is not a subclass of "Redis", "RedisArray", "RedisCluster" nor "Predis\\ClientInterface".', $class));
        } else {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Class "%s" does not exist.', $class));
        }
        return $redis;
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch(array $ids)
    {
        if (!$ids) {
            return [];
        }
        $result = [];
        if ($this->redis instanceof \ECSPrefix20210507\Predis\ClientInterface && $this->redis->getConnection() instanceof \ECSPrefix20210507\Predis\Connection\Aggregate\ClusterInterface) {
            $values = $this->pipeline(function () use($ids) {
                foreach ($ids as $id) {
                    (yield 'get' => [$id]);
                }
            });
        } else {
            $values = $this->redis->mget($ids);
            if (!\is_array($values) || \count($values) !== \count($ids)) {
                return [];
            }
            $values = \array_combine($ids, $values);
        }
        foreach ($values as $id => $v) {
            if ($v) {
                $result[$id] = $this->marshaller->unmarshall($v);
            }
        }
        return $result;
    }
    /**
     * {@inheritdoc}
     * @param string $id
     */
    protected function doHave($id)
    {
        return (bool) $this->redis->exists($id);
    }
    /**
     * {@inheritdoc}
     * @param string $namespace
     */
    protected function doClear($namespace)
    {
        $cleared = \true;
        if ($this->redis instanceof \ECSPrefix20210507\Predis\ClientInterface) {
            $evalArgs = [0, $namespace];
        } else {
            $evalArgs = [[$namespace], 0];
        }
        $hosts = $this->getHosts();
        $host = \reset($hosts);
        if ($host instanceof \ECSPrefix20210507\Predis\Client && $host->getConnection() instanceof \ECSPrefix20210507\Predis\Connection\Aggregate\ReplicationInterface) {
            // Predis supports info command only on the master in replication environments
            $hosts = [$host->getClientFor('master')];
        }
        foreach ($hosts as $host) {
            if (!isset($namespace[0])) {
                $cleared = $host->flushDb() && $cleared;
                continue;
            }
            $info = $host->info('Server');
            $info = isset($info['Server']) ? $info['Server'] : $info;
            if (!\version_compare($info['redis_version'], '2.8', '>=')) {
                // As documented in Redis documentation (http://redis.io/commands/keys) using KEYS
                // can hang your server when it is executed against large databases (millions of items).
                // Whenever you hit this scale, you should really consider upgrading to Redis 2.8 or above.
                $unlink = \version_compare($info['redis_version'], '4.0', '>=') ? 'UNLINK' : 'DEL';
                $cleared = $host->eval("local keys=redis.call('KEYS',ARGV[1]..'*') for i=1,#keys,5000 do redis.call('{$unlink}',unpack(keys,i,math.min(i+4999,#keys))) end return 1", $evalArgs[0], $evalArgs[1]) && $cleared;
                continue;
            }
            $cursor = null;
            do {
                $keys = $host instanceof \ECSPrefix20210507\Predis\ClientInterface ? $host->scan($cursor, 'MATCH', $namespace . '*', 'COUNT', 1000) : $host->scan($cursor, $namespace . '*', 1000);
                if (isset($keys[1]) && \is_array($keys[1])) {
                    $cursor = $keys[0];
                    $keys = $keys[1];
                }
                if ($keys) {
                    $this->doDelete($keys);
                }
            } while ($cursor = (int) $cursor);
        }
        return $cleared;
    }
    /**
     * {@inheritdoc}
     */
    protected function doDelete(array $ids)
    {
        if (!$ids) {
            return \true;
        }
        if ($this->redis instanceof \ECSPrefix20210507\Predis\ClientInterface && $this->redis->getConnection() instanceof \ECSPrefix20210507\Predis\Connection\Aggregate\ClusterInterface) {
            static $del;
            $del = isset($del) ? $del : (\class_exists(\ECSPrefix20210507\Predis\Command\Redis\UNLINK::class) ? 'unlink' : 'del');
            $this->pipeline(function () use($ids, $del) {
                foreach ($ids as $id) {
                    (yield $del => [$id]);
                }
            })->rewind();
        } else {
            static $unlink = \true;
            if ($unlink) {
                try {
                    $unlink = \false !== $this->redis->unlink($ids);
                } catch (\Throwable $e) {
                    $unlink = \false;
                }
            }
            if (!$unlink) {
                $this->redis->del($ids);
            }
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param int $lifetime
     */
    protected function doSave(array $values, $lifetime)
    {
        if (!($values = $this->marshaller->marshall($values, $failed))) {
            return $failed;
        }
        $results = $this->pipeline(function () use($values, $lifetime) {
            foreach ($values as $id => $value) {
                if (0 >= $lifetime) {
                    (yield 'set' => [$id, $value]);
                } else {
                    (yield 'setEx' => [$id, $lifetime, $value]);
                }
            }
        });
        foreach ($results as $id => $result) {
            if (\true !== $result && (!$result instanceof \ECSPrefix20210507\Predis\Response\Status || \ECSPrefix20210507\Predis\Response\Status::get('OK') !== $result)) {
                $failed[] = $id;
            }
        }
        return $failed;
    }
    /**
     * @param \Closure $generator
     * @return \Generator
     */
    private function pipeline($generator, $redis = null)
    {
        $ids = [];
        $redis = isset($redis) ? $redis : $this->redis;
        if ($redis instanceof \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisClusterProxy || $redis instanceof \RedisCluster || $redis instanceof \ECSPrefix20210507\Predis\ClientInterface && $redis->getConnection() instanceof \ECSPrefix20210507\Predis\Connection\Aggregate\RedisCluster) {
            // phpredis & predis don't support pipelining with RedisCluster
            // see https://github.com/phpredis/phpredis/blob/develop/cluster.markdown#pipelining
            // see https://github.com/nrk/predis/issues/267#issuecomment-123781423
            $results = [];
            foreach ($generator() as $command => $args) {
                $results[] = $redis->{$command}(...$args);
                $ids[] = 'eval' === $command ? $redis instanceof \ECSPrefix20210507\Predis\ClientInterface ? $args[2] : $args[1][0] : $args[0];
            }
        } elseif ($redis instanceof \ECSPrefix20210507\Predis\ClientInterface) {
            $results = $redis->pipeline(static function ($redis) use($generator, &$ids) {
                foreach ($generator() as $command => $args) {
                    $redis->{$command}(...$args);
                    $ids[] = 'eval' === $command ? $args[2] : $args[0];
                }
            });
        } elseif ($redis instanceof \RedisArray) {
            $connections = $results = $ids = [];
            foreach ($generator() as $command => $args) {
                $id = 'eval' === $command ? $args[1][0] : $args[0];
                if (!isset($connections[$h = $redis->_target($id)])) {
                    $connections[$h] = [$redis->_instance($h), -1];
                    $connections[$h][0]->multi(\Redis::PIPELINE);
                }
                $connections[$h][0]->{$command}(...$args);
                $results[] = [$h, ++$connections[$h][1]];
                $ids[] = $id;
            }
            foreach ($connections as $h => $c) {
                $connections[$h] = $c[0]->exec();
            }
            foreach ($results as $k => list($h, $c)) {
                $results[$k] = $connections[$h][$c];
            }
        } else {
            $redis->multi(\Redis::PIPELINE);
            foreach ($generator() as $command => $args) {
                $redis->{$command}(...$args);
                $ids[] = 'eval' === $command ? $args[1][0] : $args[0];
            }
            $results = $redis->exec();
        }
        foreach ($ids as $k => $id) {
            (yield $id => $results[$k]);
        }
    }
    /**
     * @return mixed[]
     */
    private function getHosts()
    {
        $hosts = [$this->redis];
        if ($this->redis instanceof \ECSPrefix20210507\Predis\ClientInterface) {
            $connection = $this->redis->getConnection();
            if ($connection instanceof \ECSPrefix20210507\Predis\Connection\Aggregate\ClusterInterface && $connection instanceof \Traversable) {
                $hosts = [];
                foreach ($connection as $c) {
                    $hosts[] = new \ECSPrefix20210507\Predis\Client($c);
                }
            }
        } elseif ($this->redis instanceof \RedisArray) {
            $hosts = [];
            foreach ($this->redis->_hosts() as $host) {
                $hosts[] = $this->redis->_instance($host);
            }
        } elseif ($this->redis instanceof \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisClusterProxy || $this->redis instanceof \RedisCluster) {
            $hosts = [];
            foreach ($this->redis->_masters() as $host) {
                $hosts[] = new \ECSPrefix20210507\Symfony\Component\Cache\Traits\RedisClusterNodeProxy($host, $this->redis);
            }
        }
        return $hosts;
    }
}
