<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Adapter;

use ECSPrefix20210508\Symfony\Component\Cache\Exception\CacheException;
use ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface;
/**
 * @author Antonio Jose Cerezo Aranda <aj.cerezo@gmail.com>
 */
class CouchbaseBucketAdapter extends \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AbstractAdapter
{
    const THIRTY_DAYS_IN_SECONDS = 2592000;
    const MAX_KEY_LENGTH = 250;
    const KEY_NOT_FOUND = 13;
    const VALID_DSN_OPTIONS = ['operationTimeout', 'configTimeout', 'configNodeTimeout', 'n1qlTimeout', 'httpTimeout', 'configDelay', 'htconfigIdleTimeout', 'durabilityInterval', 'durabilityTimeout'];
    private $bucket;
    private $marshaller;
    /**
     * @param string $namespace
     * @param int $defaultLifetime
     */
    public function __construct(\ECSPrefix20210508\CouchbaseBucket $bucket, $namespace = '', $defaultLifetime = 0, \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller = null)
    {
        if (\is_object($namespace)) {
            $namespace = (string) $namespace;
        }
        if (!static::isSupported()) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\CacheException('Couchbase >= 2.6.0 < 3.0.0 is required.');
        }
        $this->maxIdLength = static::MAX_KEY_LENGTH;
        $this->bucket = $bucket;
        parent::__construct($namespace, $defaultLifetime);
        $this->enableVersioning();
        $this->marshaller = isset($marshaller) ? $marshaller : new \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\DefaultMarshaller();
    }
    /**
     * @param array|string $servers
     * @return \CouchbaseBucket
     */
    public static function createConnection($servers, array $options = [])
    {
        if (\is_string($servers)) {
            $servers = [$servers];
        } elseif (!\is_array($servers)) {
            throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be array or string, "%s" given.', __METHOD__, \get_debug_type($servers)));
        }
        if (!static::isSupported()) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\CacheException('Couchbase >= 2.6.0 < 3.0.0 is required.');
        }
        \set_error_handler(function ($type, $msg, $file, $line) {
            throw new \ErrorException($msg, 0, $type, $file, $line);
        });
        $dsnPattern = '/^(?<protocol>couchbase(?:s)?)\\:\\/\\/(?:(?<username>[^\\:]+)\\:(?<password>[^\\@]{6,})@)?' . '(?<host>[^\\:]+(?:\\:\\d+)?)(?:\\/(?<bucketName>[^\\?]+))(?:\\?(?<options>.*))?$/i';
        $newServers = [];
        $protocol = 'couchbase';
        try {
            $options = self::initOptions($options);
            $username = $options['username'];
            $password = $options['password'];
            foreach ($servers as $dsn) {
                if (0 !== \strpos($dsn, 'couchbase:')) {
                    throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Invalid Couchbase DSN: "%s" does not start with "couchbase:".', $dsn));
                }
                \preg_match($dsnPattern, $dsn, $matches);
                $username = $matches['username'] ?: $username;
                $password = $matches['password'] ?: $password;
                $protocol = $matches['protocol'] ?: $protocol;
                if (isset($matches['options'])) {
                    $optionsInDsn = self::getOptions($matches['options']);
                    foreach ($optionsInDsn as $parameter => $value) {
                        $options[$parameter] = $value;
                    }
                }
                $newServers[] = $matches['host'];
            }
            $connectionString = $protocol . '://' . \implode(',', $newServers);
            $client = new \ECSPrefix20210508\CouchbaseCluster($connectionString);
            $client->authenticateAs($username, $password);
            $bucket = $client->openBucket($matches['bucketName']);
            unset($options['username'], $options['password']);
            foreach ($options as $option => $value) {
                if (!empty($value)) {
                    $bucket->{$option} = $value;
                }
            }
            return $bucket;
        } finally {
            \restore_error_handler();
        }
    }
    /**
     * @return bool
     */
    public static function isSupported()
    {
        return \extension_loaded('couchbase') && \version_compare(\phpversion('couchbase'), '2.6.0', '>=') && \version_compare(\phpversion('couchbase'), '3.0', '<');
    }
    /**
     * @param string $options
     * @return mixed[]
     */
    private static function getOptions($options)
    {
        if (\is_object($options)) {
            $options = (string) $options;
        }
        $results = [];
        $optionsInArray = \explode('&', $options);
        foreach ($optionsInArray as $option) {
            list($key, $value) = \explode('=', $option);
            if (\in_array($key, static::VALID_DSN_OPTIONS, \true)) {
                $results[$key] = $value;
            }
        }
        return $results;
    }
    /**
     * @return mixed[]
     */
    private static function initOptions(array $options)
    {
        $options['username'] = isset($options['username']) ? $options['username'] : '';
        $options['password'] = isset($options['password']) ? $options['password'] : '';
        $options['operationTimeout'] = isset($options['operationTimeout']) ? $options['operationTimeout'] : 0;
        $options['configTimeout'] = isset($options['configTimeout']) ? $options['configTimeout'] : 0;
        $options['configNodeTimeout'] = isset($options['configNodeTimeout']) ? $options['configNodeTimeout'] : 0;
        $options['n1qlTimeout'] = isset($options['n1qlTimeout']) ? $options['n1qlTimeout'] : 0;
        $options['httpTimeout'] = isset($options['httpTimeout']) ? $options['httpTimeout'] : 0;
        $options['configDelay'] = isset($options['configDelay']) ? $options['configDelay'] : 0;
        $options['htconfigIdleTimeout'] = isset($options['htconfigIdleTimeout']) ? $options['htconfigIdleTimeout'] : 0;
        $options['durabilityInterval'] = isset($options['durabilityInterval']) ? $options['durabilityInterval'] : 0;
        $options['durabilityTimeout'] = isset($options['durabilityTimeout']) ? $options['durabilityTimeout'] : 0;
        return $options;
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch(array $ids)
    {
        $resultsCouchbase = $this->bucket->get($ids);
        $results = [];
        foreach ($resultsCouchbase as $key => $value) {
            if (null !== $value->error) {
                continue;
            }
            $results[$key] = $this->marshaller->unmarshall($value->value);
        }
        return $results;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    protected function doHave($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return \false !== $this->bucket->get($id);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    protected function doClear($namespace)
    {
        if (\is_object($namespace)) {
            $namespace = (string) $namespace;
        }
        if ('' === $namespace) {
            $this->bucket->manager()->flush();
            return \true;
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    protected function doDelete(array $ids)
    {
        $results = $this->bucket->remove(\array_values($ids));
        foreach ($results as $key => $result) {
            if (null !== $result->error && static::KEY_NOT_FOUND !== $result->error->getCode()) {
                continue;
            }
            unset($results[$key]);
        }
        return 0 === \count($results);
    }
    /**
     * {@inheritdoc}
     */
    protected function doSave(array $values, $lifetime)
    {
        if (!($values = $this->marshaller->marshall($values, $failed))) {
            return $failed;
        }
        $lifetime = $this->normalizeExpiry($lifetime);
        $ko = [];
        foreach ($values as $key => $value) {
            $result = $this->bucket->upsert($key, $value, ['expiry' => $lifetime]);
            if (null !== $result->error) {
                $ko[$key] = $result;
            }
        }
        return [] === $ko ? \true : $ko;
    }
    /**
     * @param int $expiry
     * @return int
     */
    private function normalizeExpiry($expiry)
    {
        if ($expiry && $expiry > static::THIRTY_DAYS_IN_SECONDS) {
            $expiry += \time();
        }
        return $expiry;
    }
}
