<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use ECSPrefix202306\Doctrine\DBAL\DriverManager;
use ECSPrefix202306\Relay\Relay;
use ECSPrefix202306\Symfony\Component\Cache\Adapter\AbstractAdapter;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SessionHandlerFactory
{
    /**
     * @param object|string $connection
     */
    public static function createHandler($connection, array $options = []) : AbstractSessionHandler
    {
        if ($query = \is_string($connection) ? \parse_url($connection) : \false) {
            \parse_str($query['query'] ?? '', $query);
            if (($options['ttl'] ?? null) instanceof \Closure) {
                $query['ttl'] = $options['ttl'];
            }
        }
        $options = ($query ?: []) + $options;
        switch (\true) {
            case $connection instanceof \Redis:
            case $connection instanceof Relay:
            case $connection instanceof \RedisArray:
            case $connection instanceof \RedisCluster:
            case $connection instanceof \ECSPrefix202306\Predis\ClientInterface:
                return new RedisSessionHandler($connection);
            case $connection instanceof \Memcached:
                return new MemcachedSessionHandler($connection);
            case $connection instanceof \PDO:
                return new PdoSessionHandler($connection);
            case !\is_string($connection):
                throw new \InvalidArgumentException(\sprintf('Unsupported Connection: "%s".', \get_debug_type($connection)));
            case \strncmp($connection, 'file://', \strlen('file://')) === 0:
                $savePath = \substr($connection, 7);
                return new StrictSessionHandler(new NativeFileSessionHandler('' === $savePath ? null : $savePath));
            case \strncmp($connection, 'redis:', \strlen('redis:')) === 0:
            case \strncmp($connection, 'rediss:', \strlen('rediss:')) === 0:
            case \strncmp($connection, 'memcached:', \strlen('memcached:')) === 0:
                if (!\class_exists(AbstractAdapter::class)) {
                    throw new \InvalidArgumentException('Unsupported Redis or Memcached DSN. Try running "composer require symfony/cache".');
                }
                $handlerClass = \strncmp($connection, 'memcached:', \strlen('memcached:')) === 0 ? MemcachedSessionHandler::class : RedisSessionHandler::class;
                $connection = AbstractAdapter::createConnection($connection, ['lazy' => \true]);
                return new $handlerClass($connection, \array_intersect_key($options, ['prefix' => 1, 'ttl' => 1]));
            case \strncmp($connection, 'pdo_oci://', \strlen('pdo_oci://')) === 0:
                if (!\class_exists(DriverManager::class)) {
                    throw new \InvalidArgumentException('Unsupported PDO OCI DSN. Try running "composer require doctrine/dbal".');
                }
                $connection = DriverManager::getConnection(['url' => $connection])->getWrappedConnection();
            // no break;
            case \strncmp($connection, 'mssql://', \strlen('mssql://')) === 0:
            case \strncmp($connection, 'mysql://', \strlen('mysql://')) === 0:
            case \strncmp($connection, 'mysql2://', \strlen('mysql2://')) === 0:
            case \strncmp($connection, 'pgsql://', \strlen('pgsql://')) === 0:
            case \strncmp($connection, 'postgres://', \strlen('postgres://')) === 0:
            case \strncmp($connection, 'postgresql://', \strlen('postgresql://')) === 0:
            case \strncmp($connection, 'sqlsrv://', \strlen('sqlsrv://')) === 0:
            case \strncmp($connection, 'sqlite://', \strlen('sqlite://')) === 0:
            case \strncmp($connection, 'sqlite3://', \strlen('sqlite3://')) === 0:
                return new PdoSessionHandler($connection, $options);
        }
        throw new \InvalidArgumentException(\sprintf('Unsupported Connection: "%s".', $connection));
    }
}
